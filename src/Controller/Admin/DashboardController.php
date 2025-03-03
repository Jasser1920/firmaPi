<?php

namespace App\Controller\Admin;

use App\Entity\Reclammation;
use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Controller\Admin\UtilisateurCrudController;
use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\Don;
use App\Entity\Evenemment;
use App\Entity\Livraison;
use App\Entity\Location;
use App\Entity\Produit;
use App\Entity\Terrain;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($adminUrlGenerator->setController(UtilisateurCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('FirmaPi');
    }

    public function configureMenuItems(): iterable
    {   yield MenuItem::linkToCrud('Reclamation', 'fa fa-exclamation-triangle', Reclammation::class);
        yield MenuItem::linkToCrud('User', 'fas fa-user', Utilisateur::class);
        yield MenuItem::linkToCrud('Commande', 'fas fa-user', Commande::class);
        yield MenuItem::linkToCrud('Livraison', 'fas fa-user', Livraison::class);
        yield MenuItem::linkToCrud('Terrain', 'fas fa-user', Terrain::class);
        yield MenuItem::linkToCrud('Location', 'fas fa-user', Location::class);
        yield MenuItem::linkToCrud('Produit', 'fas fa-user', Produit::class);
        yield MenuItem::linkToCrud('Categorie', 'fas fa-user', Categorie::class);
        yield MenuItem::linkToCrud('Evenemment', 'fas fa-user', Evenemment::class);
        yield MenuItem::linkToCrud('Don', 'fas fa-user', Don::class);
        // Add a logout button to the user menu
   yield MenuItem::section('Account');
   yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
    }
}
