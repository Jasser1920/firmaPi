<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NavBarController extends AbstractController
{
    #[Route('/navbar', name: 'app_nav_bar')]
    public function index(): Response
    {
        return $this->render('home/nav_bar.html.twig', [
            'controller_name' => 'NavBarController',
        ]);
    }
   
}
