<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfiledetailsController extends AbstractController
{
    #[Route('/profiledetails', name: 'app_profiledetails')]
    public function index(): Response
    {
        return $this->render('profiledetails/index.html.twig', [
            'controller_name' => 'ProfiledetailsController',
        ]);
    }
}
