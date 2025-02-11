<?php

namespace App\Form;

use App\Entity\Reclammation;
use App\Enum\StatutReclammation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclammationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'En attente' => StatutReclammation::EN_ATTENTE,
                    'En cours'   => StatutReclammation::EN_COURS,
                    'Résolue'    => StatutReclammation::RESOLUE,
                ],
                'choice_label' => fn($choice) => $choice->label(), // Show readable labels
                'choice_value' => fn(?StatutReclammation $enum) => $enum?->value, // Convert enum to string
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclammation::class,
        ]);
    }
}
