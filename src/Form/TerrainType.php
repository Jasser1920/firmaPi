<?php

namespace App\Form;

use App\Entity\Terrain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('localisation', TextType::class, [
                'label' => 'Localisation',
                'attr' => ['placeholder' => 'Entrez la localisation'],
                'required' => true,
            ])
            ->add('prix_location', NumberType::class, [
                'label' => 'Prix de location (DT)',
                'attr' => ['placeholder' => 'Entrez le prix de location'],
                'required' => true,
            ])
            ->add('disponibilite', CheckboxType::class, [
                'label' => 'Disponible ?',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Terrain::class,
        ]);
    }
}
