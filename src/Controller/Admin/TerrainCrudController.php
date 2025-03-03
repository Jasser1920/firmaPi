<?php

namespace App\Controller\Admin;

use App\Entity\Terrain;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TerrainCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Terrain::class;
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
        // Disable the "new" action
        return $actions
            ->disable('new');
    }

    public function configureCrud(Crud $crud): Crud
    {
        // Optionally, you can customize the CRUD page title, labels, etc.
        return $crud
            ->setPageTitle('index', 'Liste des Terrains')
            ->setPageTitle('detail', 'Détails du Terrain')
            ->setEntityLabelInSingular('Terrain')
            ->setEntityLabelInPlural('Terrains');
    }
}