<?php

namespace App\Form;

use App\Entity\Agriculteur;
use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('image')
            ->add('quantite')
            ->add('unite')
            ->add('prix')
            ->add('dateExp', null, [
                'widget' => 'single_text'
            ])
            ->add('agriculteur', EntityType::class, [
                'class' => Agriculteur::class,
'choice_label' => 'id',
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
