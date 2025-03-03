<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Enum\StatutCommande;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField; // Use NumberField for total
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class CommandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commande::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setDisabled(), // ID is read-only
            DateField::new('date_commande', 'Date de Commande')
                ->setFormat('yyyy-MM-dd') // Optional: Customize the date format
                ->setDisabled(), // Disable editing
            NumberField::new('total', 'Total') // Use NumberField for total
                ->setNumDecimals(2) // Optional: Set the number of decimal places
                ->setDisabled(), // Disable editing
            ChoiceField::new('statut', 'Statut')
                ->setChoices($this->getStatutChoices()) // Use enum cases as choices
                ->renderAsNativeWidget() // Render as a dropdown
                ->setDisabled(), // Disable editing
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Commandes')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Détails de la Commande')
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE); // Disable Add, Edit, Delete actions
    }

    /**
     * Get the choices for the "statut" field using the StatutCommande enum.
     *
     * @return array<string, string>
     */
    private function getStatutChoices(): array
    {
        $choices = [];
        foreach (StatutCommande::cases() as $case) {
            $choices[$case->label()] = $case->value; // Use the label as the key and the value as the value
        }
        return $choices;
    }
}