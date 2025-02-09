<?php

namespace App\Controller;

use App\Form\ProfileEditFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
final class ProfilEditController extends AbstractController
{
    #[Route('/profil/edit', name: 'app_profil_edit')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileEditFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Your profile has been updated.');

            return $this->redirectToRoute('app_home');
        }
        return $this->render('profil_edit/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
