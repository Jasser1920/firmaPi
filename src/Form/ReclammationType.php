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
                'choice_label' => function ($choice, $key, $value) {
                    return $choice->name; // Affiche les noms des enums comme 'EN_ATTENTE', 'EN_COURS', etc.
                },
                'expanded' => false, // false = menu déroulant, true = boutons radio
                'multiple' => false, // false = une seule sélection
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclammation::class,
        ]);
    }
}
