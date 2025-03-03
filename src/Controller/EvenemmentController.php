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
    public function index(EvenemmentRepository $evenementRepository): Response
    {
        $user = $this->getUser();
        $events = $user && in_array('ROLE_' . Role::ASSOCIATION->value, $user->getRoles())
            ? $evenementRepository->findBy(['utilisateur' => $user], ['date' => 'ASC'])
            : $evenementRepository->findBy([], ['date' => 'ASC']);
        
        return $this->render('evenemment/index.html.twig', ['evenemments' => $events]);
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
                    $this->addFlash('error', 'Failed to upload event image: ' . $e->getMessage());
                    return $this->redirectToRoute('app_evenemment_new');
                }
            }
            
            $entityManager->persist($evenement);
            $entityManager->flush();
            return $this->redirectToRoute('app_evenemment_index');
        }
        
        return $this->render('evenemment/new.html.twig', ['evenement' => $evenement, 'form' => $form]);
    }

    #[Route('/{id}', name: 'app_evenemment_show', methods: ['GET'])]
    public function show(Evenemment $evenement): Response
    {
        return $this->render('evenemment/show.html.twig', ['evenement' => $evenement]);
    }

    #[Route('/{id}/edit', name: 'app_evenemment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenemment $evenement, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_' . Role::ASSOCIATION->value);
        if ($evenement->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
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
                    $this->addFlash('error', 'Failed to upload event image: ' . $e->getMessage());
                }
            }
            
            $entityManager->flush();
            return $this->redirectToRoute('app_evenemment_index');
        }
        
        return $this->render('evenemment/edit.html.twig', ['evenement' => $evenement, 'form' => $form]);
    }

    #[Route('/{id}', name: 'app_evenemment_delete', methods: ['POST'])]
    public function delete(Request $request, Evenemment $evenement, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_' . Role::ASSOCIATION->value);
        if ($evenement->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
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

}
