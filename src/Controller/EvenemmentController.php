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

#[Route('/evenemment')]
final class EvenemmentController extends AbstractController
{
    #[Route(name: 'app_evenemment_index', methods: ['GET'])]
    public function index(EvenemmentRepository $evenementRepository): Response
    {
        $user = $this->getUser();
        
        if ($user && in_array('ROLE_' . Role::ASSOCIATION->value, $user->getRoles())) {
            $events = $evenementRepository->findBy(['utilisateur' => $user], ['date' => 'ASC']);
        } else {
            $events = $evenementRepository->findAll(['date' => 'ASC']);
        }

        return $this->render('evenemment/index.html.twig', [
            'evenemments' => $events,
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
            $imageFile = $evenement->getImageFile();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
                $evenement->setImage('/uploads/images/'.$newFilename);
            }

            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenemment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenemment/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenemment_show', methods: ['GET'])]
    public function show(Evenemment $evenement): Response
    {
        return $this->render('evenemment/show.html.twig', [
            'evenement' => $evenement,
        ]);
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
            $imageFile = $evenement->getImageFile();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
                $evenement->setImage('/uploads/images/'.$newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_evenemment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenemment/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
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

        return $this->redirectToRoute('app_evenemment_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/participate', name: 'app_evenemment_participate', methods: ['POST'])]
    public function participate(Request $request, Evenemment $evenement, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('participate'.$evenement->getId(), $request->getPayload()->getString('_token'))) {
            $participation = new Participation();
            $participation->setUser($user);
            $participation->setEvenement($evenement);
            
            $entityManager->persist($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenemment_index');
    }
}