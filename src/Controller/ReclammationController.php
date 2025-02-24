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
    public function index(ReclammationRepository $reclammationRepository): Response
    {
        $user = $this->security->getUser();
        $reclammations = $user instanceof Utilisateur 
            ? $reclammationRepository->findBy(['utilisateur' => $user]) 
            : [];
    
        return $this->render('reclammation/index.html.twig', [
            'reclammations' => $reclammations,
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
        
    // Set the date_creation field to the current date and time
    $reclammation->setDateCreation(new \DateTime());

    // Set the statut field to a default value (e.g., the first enum value)
    $reclammation->setStatut(StatutReclammation::EN_ATTENTE);
    
        $form = $this->createForm(ReclammationType::class, $reclammation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclammation);
            $entityManager->flush();
    
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

            return $this->redirectToRoute('app_reclammation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclammation/edit.html.twig', [
            'reclammation' => $reclammation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclammation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclammation $reclammation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclammation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reclammation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclammation_index', [], Response::HTTP_SEE_OTHER);
    }
}
