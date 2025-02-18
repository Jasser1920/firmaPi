<?php

namespace App\Form;

use App\Entity\Reclammation;
use App\Entity\Utilisateur;
use App\Enum\StatutReclammation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReclammationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Décrivez votre réclamation'],
            ])
            ->add('date_creation', null, [
                'widget' => 'single_text',
            ])
            ->add('statut', EnumType::class, [
                'class' => StatutReclammation::class,
                'choice_label' => fn (StatutReclammation $statut) => $statut->label(),
                'placeholder' => 'Sélectionner un statut',
                'required' => true,
            ])
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'id',
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclammation::class,
        ]);
    }
}
