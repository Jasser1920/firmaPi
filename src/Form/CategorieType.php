<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_categorie', ChoiceType::class, [
                'choices' => [
                    'Fruits' => 'Fruits',
                    'Légumes' => 'Légumes',
                    'Céréales' => 'Céréales',
                    'Fruits secs' => 'Fruits secs',
                    'Apiculture' => 'Apiculture',
                    'Oeufs' => 'Oeufs',
                    'Produits laitiers' => 'Produits laitiers',
                    'Viande' => 'Viande',
                    'Volaille' => 'Volaille',
                    'Plantes aromatiques' => 'Plantes aromatiques',
                    'Plantes médicales' => 'Plantes médicales',
                    'Champignons' => 'Champignons',
                    'Huiles' => 'Huiles',
                    'Épices' => 'Épices',
                ],
                'placeholder' => 'Sélectionnez une catégorie',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
