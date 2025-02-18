<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom du produit'],
            ])
            ->add('prix', NumberType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Prix du produit'],
            ])
            ->add('description', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Description du produit'],
            ])
            ->add('image', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'URL de l\'image'],
            ])
            ->add('quantite', NumberType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Quantité disponible'],
            ])
            ->add('date_expiration', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nomCategorie', // Affiche le nom de la catégorie
                'placeholder' => 'Sélectionnez une catégorie',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
