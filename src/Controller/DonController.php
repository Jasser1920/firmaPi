<?php

namespace App\Controller;

use App\Entity\Don;
use App\Entity\Participation;
use App\Form\DonType;
use App\Repository\DonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/don')]
final class DonController extends AbstractController
{
    #[Route(name: 'app_don_index', methods: ['GET'])]
    public function index(DonRepository $donRepository): Response
    {
        return $this->render('don/index.html.twig', [
            'dons' => $donRepository->findAll(),
        ]);
    }

    #[Route('/new/{eventId}', name: 'app_don_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, int $eventId): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $participation = $entityManager->getRepository(Participation::class)->findOneBy([
            'user' => $user,
            'evenement' => $eventId,
        ]);

        if (!$participation) {
            throw $this->createAccessDeniedException('Vous devez participer à cet événement pour faire un don.');
        }

        $don = new Don();
        $don->setEvenement($participation->getEvenement());
        $don->setDonsUser($user);
        $form = $this->createForm(DonType::class, $don);
        $form->handleRequest($request);

        // Log form submission details
        if ($form->isSubmitted()) {
            dump('Form submitted at: ' . (new \DateTime())->format('Y-m-d H:i:s'));
            dump('Form data:', $form->getData());
            dump('Form is valid:', $form->isValid());
            if (!$form->isValid()) {
                dump('Form errors:', $form->getErrors(true, true));
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            dump('Persisting Don:', $don);
            $entityManager->persist($don);
            $entityManager->flush();
            dump('Database flush completed');

            return $this->redirectToRoute('app_evenemment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('don/new.html.twig', [
            'don' => $don,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_don_show', methods: ['GET'])]
    public function show(Don $don): Response
    {
        return $this->render('don/show.html.twig', [
            'don' => $don,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_don_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Don $don, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DonType::class, $don);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_don_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('don/edit.html.twig', [
            'don' => $don,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_don_delete', methods: ['POST'])]
    public function delete(Request $request, Don $don, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$don->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($don);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_don_index', [], Response::HTTP_SEE_OTHER);
    }
}