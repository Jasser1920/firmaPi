<?php

namespace App\Form;

use App\Entity\Terrain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TerrainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Entrez la description du terrain'],
                'required' => true,
            ])
            ->add('superficie', NumberType::class, [
                'label' => 'Superficie (m²)',
                'attr' => ['placeholder' => 'Entrez la superficie'],
                'required' => true,
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'attr' => ['readonly' => true],
                'required' => false,
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'attr' => ['readonly' => true],
                'required' => false,
            ])
            ->add('prix_location', NumberType::class, [
                'label' => 'Prix de location (DT)',
                'attr' => ['placeholder' => 'Entrez le prix de location'],
                'required' => true,
            ])
            ->add('disponibilite', CheckboxType::class, [
                'label' => 'Disponible ?',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Terrain::class,
        ]);
    }
}