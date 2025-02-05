<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FooterController extends AbstractController
{
    #[Route('/footer', name: 'app_footer')]
    public function index(): Response
    {
        return $this->render('home/footer.html.twig', [
            'controller_name' => 'FooterController',
        ]);
    }
}
