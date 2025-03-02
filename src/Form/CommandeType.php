<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Livraison;
use App\Entity\Produit;
use App\Enum\StatutCommande;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_commande', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('total', NumberType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    StatutCommande::EN_ATTENTE,
                    StatutCommande::EN_COURS,
                    StatutCommande::RESOLUE,
                    StatutCommande::REJETEE,
                ],
                'choice_label' => fn(StatutCommande $choice) => $choice->label(),
                'choice_value' => fn(StatutCommande $choice) => $choice->value,
                'placeholder' => 'Sélectionner un statut',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('produits', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('livraison', EntityType::class, [
                'class' => Livraison::class,
                'choice_label' => 'nom_societe',
                'placeholder' => 'Sélectionner une société de livraison',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}   