<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(), // Hide ID field on forms (edit/new)
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        // Disable the "new" and "edit" actions
        return $actions
            ->disable('new', 'edit');
    }

    public function configureCrud(Crud $crud): Crud
    {
        // Optionally, you can customize the CRUD page title, labels, etc.
        return $crud
            ->setPageTitle('index', 'Liste des Produits')
            ->setPageTitle('detail', 'Détails du Produit')
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits');
    }
}