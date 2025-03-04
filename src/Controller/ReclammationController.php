<?php

namespace App\Controller;

use App\Entity\Reclammation;
use App\Entity\Utilisateur;
use App\Enum\StatutReclammation;
use App\Form\ReclammationType;
use App\Repository\ReclammationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/reclammation')]
final class ReclammationController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route(name: 'app_reclammation_index', methods: ['GET'])]
    public function index(Request $request, ReclammationRepository $reclammationRepository): Response
    {
        $user = $this->security->getUser();
        
        // Get search and filter parameters
        $search = $request->query->get('search', '');
        $statusFilter = $request->query->get('status', '');

        // Build query
        $qb = $reclammationRepository->createQueryBuilder('r');
        
        // Filter by user if authenticated
        if ($user instanceof Utilisateur) {
            $qb->andWhere('r.utilisateur = :user')
               ->setParameter('user', $user);
        } else {
            $qb->andWhere('1 = 0'); // Return empty result if no user
        }

        // Search by title
        if ($search) {
            $qb->andWhere('r.titre LIKE :search')
               ->setParameter('search', "%$search%");
        }

        // Filter by status
        if ($statusFilter) {
            try {
                $statusEnum = StatutReclammation::from($statusFilter);
                $qb->andWhere('r.statut = :status')
                   ->setParameter('status', $statusEnum);
            } catch (\ValueError $e) {
                // Invalid status value; ignore the filter
            }
        }

        // Order by date_creation
        $qb->orderBy('r.date_creation', 'DESC');
        
        $reclammations = $qb->getQuery()->getResult();

        // Get all possible status values for the filter
        $statusCases = StatutReclammation::cases();

        return $this->render('reclammation/index.html.twig', [
            'reclammations' => $reclammations,
            'search' => $search,
            'status' => $statusFilter,
            'status_cases' => $statusCases,
        ]);
    }

    #[Route('/new', name: 'app_reclammation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $reclammation = new Reclammation();
    
        // Get the currently logged-in user
        $user = $security->getUser();
    
        // Set the utilisateur field to the currently logged-in user
        if ($user instanceof Utilisateur) {
            $reclammation->setUtilisateur($user);
        }
        
        // Set the date_creation field to the current date and time (already set in constructor)
        $reclammation->setDateCreation(new \DateTime());

        // Set the statut field to a default value (already set in constructor)
        $reclammation->setStatut(StatutReclammation::EN_ATTENTE);
    
        $form = $this->createForm(ReclammationType::class, $reclammation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclammation);
            $entityManager->flush();
            $this->addFlash('success', 'Réclamation créée avec succès!');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('reclammation/new.html.twig', [
            'reclammation' => $reclammation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_reclammation_show', methods: ['GET'])]
    public function show(Reclammation $reclammation): Response
    {
        return $this->render('reclammation/show.html.twig', [
            'reclammation' => $reclammation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclammation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclammation $reclammation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclammationType::class, $reclammation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Réclamation modifiée avec succès!');
            return $this->redirectToRoute('app_reclammation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclammation/edit.html.twig', [
            'reclammation' => $reclammation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_reclammation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclammation $reclammation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclammation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reclammation);
            $entityManager->flush();
            $this->addFlash('success', 'Réclamation supprimée avec succès!');
        }

        return $this->redirectToRoute('app_reclammation_index', [], Response::HTTP_SEE_OTHER);
    }
}