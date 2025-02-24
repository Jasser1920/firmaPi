<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Enum\Role;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Doctrine\ORM\EntityManagerInterface;
class UtilisateurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }

  
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('email'),
            TextField::new('motdepasse')->onlyOnForms(),
            ChoiceField::new('role')
            ->setChoices([
                'Admin' => Role::ADMIN,
                'Agriculture' => Role::AGRICULTURE,
                'Client' => Role::CLIENT,
                'Association' => Role::ASSOCIATION,
            ])
            ->renderAsNativeWidget(),
            TextField::new('nom'),
            TextField::new('prenom'),
            TextField::new('adresse'),
            TextField::new('telephone'),
            BooleanField::new('blocked')->renderAsSwitch(false), // Show blocked status
        ];
    }
    public function configureActions(Actions $actions): Actions
{
    // Add block/unblock action
    $blockAction = Action::new('blockUnblock', 'Block/Unblock', 'fa fa-ban')
        ->linkToCrudAction('blockUnblockUser')
        ->displayIf(fn(Utilisateur $user) => $user->isBlocked() ? 'Unblock' : 'Block');

    return $actions
       
        ->add(Crud::PAGE_INDEX, $blockAction); // Add custom block/unblock action
}
    public function blockUnblockUser(AdminContext $context, EntityManagerInterface $entityManager)
    {
        $user = $context->getEntity()->getInstance();
        $user->setBlocked(!$user->isBlocked()); // Toggle block status

        $entityManager->flush(); // Save changes to the database

        $this->addFlash('success', $user->isBlocked() ? 'User blocked' : 'User unblocked');
        return $this->redirectToRoute('admin_dashboard');
    }
    
}
