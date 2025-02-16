<?php

namespace App\Controller;

use App\Entity\Reclammation;
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
        return $this->render('reclammation/index.html.twig', [
            'reclammations' => $reclammationRepository->findAll(),
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
            'form' => $form,
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
