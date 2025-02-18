<?php

namespace App\Controller;

use App\Entity\Evenemment;
use App\Form\EvenemmentType;
use App\Repository\EvenemmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/evenemment')]
final class EvenemmentController extends AbstractController
{
    #[Route(name: 'app_evenemment_index', methods: ['GET'])]
    public function index(EvenemmentRepository $evenemmentRepository): Response
    {
        return $this->render('evenemment/index.html.twig', [
            'evenemments' => $evenemmentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_evenemment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenemment = new Evenemment();
        $form = $this->createForm(EvenemmentType::class, $evenemment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenemment);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenemment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenemment/new.html.twig', [
            'evenemment' => $evenemment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenemment_show', methods: ['GET'])]
    public function show(Evenemment $evenemment): Response
    {
        return $this->render('evenemment/show.html.twig', [
            'evenemment' => $evenemment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenemment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenemment $evenemment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenemmentType::class, $evenemment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenemment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenemment/edit.html.twig', [
            'evenemment' => $evenemment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenemment_delete', methods: ['POST'])]
    public function delete(Request $request, Evenemment $evenemment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenemment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($evenemment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenemment_index', [], Response::HTTP_SEE_OTHER);
    }
}
