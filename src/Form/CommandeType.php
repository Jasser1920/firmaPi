<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text', // Renders as a single input (HTML5 date picker)
                'label' => 'Date de Commande',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez la date',
                ],
            ])
            ->add('statut', TextType::class, [
                'label' => 'Statut de la Commande',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le statut',
                ],
            ])
            ->add('montant', NumberType::class, [
                'label' => 'Montant Total',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le montant',
                ],
            ])
            ->add('produitCommande', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nom', // Display product names instead of IDs
                'multiple' => true, // Allow selecting multiple products
                'label' => 'Produits Commandés',
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}