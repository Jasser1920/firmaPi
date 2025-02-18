<?php

namespace App\Form;

use App\Entity\Reclammation;
use App\Entity\ReponseReclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponseReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message')
            ->add('date_reponse', null, [
                'widget' => 'single_text',
            ])
            ->add('reclamation', EntityType::class, [
                'class' => Reclammation::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReponseReclamation::class,
        ]);
    }
}
