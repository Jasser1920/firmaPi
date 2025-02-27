<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateur;

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
    public function deleteProfile(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
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

        // Logout the user before deleting the account
        $this->container->get('security.token_storage')->setToken(null);
        $request->getSession()->invalidate();

        // Remove the user from the database
        $entityManager->remove($user);
        $entityManager->flush();

        // Add a flash message and redirect to the homepage
        $this->addFlash('success', 'Your account has been deleted successfully.');
        return $this->redirectToRoute('app_home');
    }
}