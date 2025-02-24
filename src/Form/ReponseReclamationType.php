<?php

namespace App\Form;

use App\Entity\Reclammation;
use App\Entity\ReponseReclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponseReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextareaType::class, [
                'attr' => ['readonly' => false], // Makes the message field read-only
            ])
            ->add('date_reponse', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['readonly' => true], // Makes the date field read-only
            ])
            ->add('reclamation', HiddenType::class, [
                'mapped' => false, // Prevents mapping to the entity to keep it hidden
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReponseReclamation::class,
        ]);
    }
}
