<?php

namespace App\Controller\Admin;

use App\Entity\Livraison;
use App\Enum\StatutLivraison;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LivraisonCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Livraison::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(), // Hide ID field on forms (edit/new)
            TextField::new('nom_societe', 'Nom de la société'),
            TextareaField::new('adresse_livraison', 'Adresse de livraison'),
            DateField::new('date_livraison', 'Date de livraison'),
            ChoiceField::new('statut', 'Statut')
                ->setChoices($this->getStatutChoices()) // Use enum cases as choices
                ->renderAsNativeWidget(), // Render as a dropdown
        ];
    }

    /**
     * Get the choices for the "statut" field using the StatutLivraison enum.
     *
     * @return array<string, string>
     */
    private function getStatutChoices(): array
    {
        $choices = [];
        foreach (StatutLivraison::cases() as $case) {
            $choices[$case->label()] = $case->value; // Use the label as the key and the value as the value
        }
        return $choices;
    }
}