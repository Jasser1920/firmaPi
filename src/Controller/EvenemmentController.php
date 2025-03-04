<?php

namespace App\Controller;

use App\Entity\Evenemment;
use App\Entity\Participation;
use App\Enum\Role;
use App\Form\EvenemmentType;
use App\Repository\EvenemmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Cloudinary\Cloudinary;

#[Route('/evenemment')]
final class EvenemmentController extends AbstractController
{
    private Cloudinary $cloudinary;

    public function __construct(Cloudinary $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    #[Route(name: 'app_evenemment_index', methods: ['GET'])]
    public function index(Request $request, EvenemmentRepository $evenementRepository): Response
    {
        $user = $this->getUser();
        $isAssociation = $user && in_array('ROLE_' . Role::ASSOCIATION->value, $user->getRoles());
        
        // Get search and filter parameters
        $search = $request->query->get('search', '');
        $daysFilter = $request->query->getInt('days', 0); // Days range
        $sort = $request->query->get('sort', 'date');
        
        // Build query criteria
        $criteria = $isAssociation ? ['utilisateur' => $user] : [];
        if ($search) {
            $criteria['titre'] = $search; // This will need a custom query for partial matching
        }

        // Date filtering logic
        $qb = $evenementRepository->createQueryBuilder('e');
        if ($isAssociation) {
            $qb->andWhere('e.utilisateur = :user')->setParameter('user', $user);
        }
        
        if ($search) {
            $qb->andWhere('e.titre LIKE :search')
               ->setParameter('search', "%$search%");
        }

        if ($daysFilter > 0) {
            $dateLimit = new \DateTime();
            if ($daysFilter === 7) { // Next week
                $dateLimit->modify('monday next week');
                $endDate = clone $dateLimit;
                $endDate->modify('+6 days');
                $qb->andWhere('e.date BETWEEN :start AND :end')
                   ->setParameter('start', $dateLimit)
                   ->setParameter('end', $endDate);
            } elseif ($daysFilter === 30) { // Next month
                $dateLimit->modify('first day of next month');
                $endDate = clone $dateLimit;
                $endDate->modify('last day of this month');
                $qb->andWhere('e.date BETWEEN :start AND :end')
                   ->setParameter('start', $dateLimit)
                   ->setParameter('end', $endDate);
            } else { // Custom days range
                $dateLimit->modify("+$daysFilter days");
                $qb->andWhere('e.date <= :date')
                   ->setParameter('date', $dateLimit);
            }
        }

        // Sorting
        $sortField = match($sort) {
            'location' => 'lieux',
            'title' => 'titre',
            default => 'date',
        };
        $qb->orderBy("e.$sortField", 'ASC');
        
        $events = $qb->getQuery()->getResult();

        return $this->render('evenemment/index.html.twig', [
            'evenemments' => $events,
            'search' => $search,
            'days' => $daysFilter,
            'sort' => $sort,
        ]);
    }

    #[Route('/new', name: 'app_evenemment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_' . Role::ASSOCIATION->value);
        
        $evenement = new Evenemment();
        $evenement->setUtilisateur($this->getUser());
        $form = $this->createForm(EvenemmentType::class, $evenement);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                try {
                    $uploadResult = $this->cloudinary->uploadApi()->upload(
                        $imageFile->getPathname(),
                        [
                            'folder' => 'event_images',
                            'public_id' => 'event_' . uniqid(),
                            'overwrite' => true,
                            'resource_type' => 'image',
                        ]
                    );
                    $evenement->setImage($uploadResult['secure_url']);
                } catch (\Cloudinary\Api\Exception\ApiError $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image: ' . $e->getMessage());
                    return $this->render('evenemment/new.html.twig', [
                        'evenement' => $evenement,
                        'form' => $form->createView(),
                    ]);
                }
            }
            
            $entityManager->persist($evenement);
            $entityManager->flush();
            $this->addFlash('success', 'Événement créé avec succès!');
            return $this->redirectToRoute('app_evenemment_index');
        }
        
        return $this->render('evenemment/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_evenemment_show', methods: ['GET'])]
    public function show(Evenemment $evenement): Response
    {
        return $this->render('evenemment/show.html.twig', [
            'evenemment' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenemment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenemment $evenement, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_' . Role::ASSOCIATION->value);
        if ($evenement->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres événements.');
        }

        $form = $this->createForm(EvenemmentType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                try {
                    $uploadResult = $this->cloudinary->uploadApi()->upload(
                        $imageFile->getPathname(),
                        [
                            'folder' => 'event_images',
                            'public_id' => 'event_' . uniqid(),
                            'overwrite' => true,
                            'resource_type' => 'image',
                        ]
                    );
                    $evenement->setImage($uploadResult['secure_url']);
                } catch (\Cloudinary\Api\Exception\ApiError $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image: ' . $e->getMessage());
                    return $this->render('evenemment/edit.html.twig', [
                        'evenement' => $evenement,
                        'form' => $form->createView(),
                    ]);
                }
            }
            
            $entityManager->flush();
            $this->addFlash('success', 'Événement modifié avec succès!');
            return $this->redirectToRoute('app_evenemment_index');
        }
        
        return $this->render('evenemment/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_evenemment_delete', methods: ['POST'])]
    public function delete(Request $request, Evenemment $evenement, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_' . Role::ASSOCIATION->value);
        if ($evenement->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez supprimer que vos propres événements.');
        }
        
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
            $this->addFlash('success', 'Événement supprimé avec succès!');
        }
        
        return $this->redirectToRoute('app_evenemment_index');
    }

    #[Route('/{id}/qr', name: 'app_evenemment_qr', methods: ['GET'])]
    public function showQrCode(Evenemment $evenemment): Response
    {
        $eventDetails = sprintf(
            "Titre: %s\nDescription: %s\nDate: %s\nLieu: %s",
            $evenemment->getTitre(),
            $evenemment->getDesecription(),
            $evenemment->getDate()->format('Y-m-d'),
            $evenemment->getLieux()
        );

        $qrCodeUrl = sprintf(
            'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=%s',
            urlencode($eventDetails)
        );

        return $this->render('evenemment/qr_code.html.twig', [
            'qr_code_url' => $qrCodeUrl,
            'evenemment' => $evenemment,
        ]);
    }

    #[Route('/{id}/participate', name: 'app_evenemment_participate', methods: ['POST'])]
    public function participate(Request $request, Evenemment $evenement, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour participer.');
            return $this->redirectToRoute('app_login');
        }

        if (!in_array('ROLE_' . Role::AGRICULTURE->value, $user->getRoles()) && 
            !in_array('ROLE_' . Role::CLIENT->value, $user->getRoles())) {
            $this->addFlash('error', 'Seuls les agriculteurs et clients peuvent participer.');
            return $this->redirectToRoute('app_evenemment_index');
        }

        if (!$this->isCsrfTokenValid('participate'.$evenement->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_evenemment_index');
        }

        // Check if user already participated
        $existingParticipation = $evenement->getParticipations()->filter(
            fn(Participation $p) => $p->getUser() === $user
        )->first();

        if ($existingParticipation) {
            $this->addFlash('warning', 'Vous participez déjà à cet événement.');
            return $this->redirectToRoute('app_evenemment_index');
        }

        $participation = new Participation();
        $participation->setUser($user);
        $participation->setEvenement($evenement);
        
        $entityManager->persist($participation);
        $entityManager->flush();

        $this->addFlash('success', 'Vous participez maintenant à cet événement!');
        return $this->redirectToRoute('app_evenemment_index');
    }
}