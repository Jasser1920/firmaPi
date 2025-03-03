<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Terrain;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début',
                'required' => true,
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
                'required' => true,
            ])
            ->add('prixTotal', NumberType::class, [
                'label' => 'Prix total',
                'required' => true,
            ])
            ->add('modePaiement', ChoiceType::class, [
                'label' => 'Mode de paiement',
                'choices' => [
                    'Non spécifié' => '',
                    'Carte bancaire' => 'Carte bancaire',
                    'Espèces' => 'Espèces',
                    'Virement bancaire' => 'Virement bancaire',
                    'Chèque' => 'Chèque',
                ],
                'required' => true,
            ])
            ->add('terrain', EntityType::class, [
                'class' => Terrain::class,
                'choice_label' => 'description',
                'label' => 'Terrain',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}