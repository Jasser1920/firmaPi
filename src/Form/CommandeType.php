<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Livraison;
use App\Entity\Produit;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Enum\StatutCommande; // Assure-toi que cette classe existe bien
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('date_commande', DateTimeType::class, [
            'widget' => 'single_text',  // Utilisez un seul champ texte
        'html5' => false,  // Désactive l'option html5
        'format' => 'yyyy-MM-dd', 
        ])
        
            ->add('total')
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En attente' => StatutCommande::EN_ATTENTE,
                    'En cours' => StatutCommande::EN_COURS,
                    'Résolue' => StatutCommande::RESOLUE,
                    'Rejetée' => StatutCommande::REJETEE,
                ],
                'choice_label' => fn ($choice) => $choice->label(), // Assure-toi que label() existe dans StatutCommande
                'placeholder' => 'Sélectionner un statut',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('produits', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('livraison', EntityType::class, [
                'class' => Livraison::class,
                'choice_label' => 'id',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}