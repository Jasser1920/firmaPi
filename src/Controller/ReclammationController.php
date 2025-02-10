<?php

namespace App\Controller;

use App\Entity\Reclammation;
use App\Enum\StatutReclammation;
use App\Form\ReclammationType;
use App\Repository\ReclammationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reclammation')]
final class ReclammationController extends AbstractController
{
    #[Route(name: 'app_reclammation_index', methods: ['GET'])]
    public function index(ReclammationRepository $reclammationRepository): Response
    {
        // Récupérer toutes les réclamations
        $reclammations = $reclammationRepository->findAll();

        // Transformer le statut de chaque réclamation en chaîne de caractères
        foreach ($reclammations as $reclammation) {
            // Récupérer le statut de la réclamation (c'est un objet de type StatutReclammation)
            $status = $reclammation->getStatus();

            // Vérifier si le statut existe et le convertir en énumération
            if ($status) {
                $statusEnum = StatutReclammation::from($status->value); // Convertir la chaîne en instance d'énumération
                $reclammation->setStatus($statusEnum); // Mettre à jour le statut
            }
        }

        // Passer les réclamations transformées à Twig
        return $this->render('reclammation/index.html.twig', [
            'reclammations' => $reclammations,
        ]);
    }

    #[Route('/new', name: 'app_reclammation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclammation = new Reclammation();
        $form = $this->createForm(ReclammationType::class, $reclammation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclammation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reclammation_index', [], Response::HTTP_SEE_OTHER);
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
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_reclammation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclammation $reclammation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclammation->getId(), $request->get('token'))) {
            $entityManager->remove($reclammation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclammation_index', [], Response::HTTP_SEE_OTHER);
    }
}
