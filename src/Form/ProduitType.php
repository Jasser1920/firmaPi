<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter product name'],
            ])
            ->add('prix', NumberType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter product price'],
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter product description'],
            ])
            ->add('image', FileType::class, [
                'label' => 'Product Image',
                'mapped' => false, // This won't map directly to the entity’s image field
                'required' => false, // Optional image upload
                'attr' => ['class' => 'form-control'],
            ])
            ->add('quantite', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter product quantity'],
            ])
            ->add('date_expiration', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nomCategorie', // Matches the getter method
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Select a category', // Optional placeholder
                'required' => false, // Allow no category to be selected
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}