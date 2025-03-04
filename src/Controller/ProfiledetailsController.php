<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateur;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfiledetailsController extends AbstractController
{
    #[Route('/profiledetails', name: 'app_profiledetails')]
    public function index(): Response
    {
        return $this->render('profiledetails/index.html.twig', [
            'controller_name' => 'ProfiledetailsController',
        ]);
    }

    #[Route('/profile/delete', name: 'app_profile_delete', methods: ['DELETE'])]
    public function deleteProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to delete your profile.');
        }

        // Check CSRF token for security
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete' . $user->getId(), $submittedToken)) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        // Remove related entities (lazy loading is triggered by iteration)
        foreach ($user->getEvenement()->toArray() as $evenement) {
            $entityManager->remove($evenement);
        }
        foreach ($user->getDons()->toArray() as $don) {
            $entityManager->remove($don);
        }
        foreach ($user->getReclamation()->toArray() as $reclamation) {
            $entityManager->remove($reclamation);
        }
        foreach ($user->getTerrain()->toArray() as $terrain) {
            $entityManager->remove($terrain);
        }
        foreach ($user->getProduit()->toArray() as $produit) {
            $entityManager->remove($produit);
        }

        // Flush to delete related entities first
        $entityManager->flush();

        // Now delete the user
        $entityManager->remove($user);
        $entityManager->flush();

        // Logout the user
        $this->container->get('security.token_storage')->setToken(null);
        $request->getSession()->invalidate();

        $this->addFlash('success', 'Your account has been deleted successfully.');
        return $this->redirectToRoute('app_home');
    }
}