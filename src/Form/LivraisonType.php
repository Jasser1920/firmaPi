<?php

namespace App\Form;

use App\Entity\Livraison;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Enum\StatutLivraison; 
class LivraisonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_societe')
            ->add('adresse_livraison')
            ->add('date_livraison', null, [
                'widget' => 'single_text',
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En attente' => StatutLivraison::EN_ATTENTE,
                    'En cours' => StatutLivraison::EN_COURS,
                    'Résolue' => StatutLivraison::RESOLUE,
                    'Rejetée' => StatutLivraison::REJETEE,
                ],
                'choice_label' => fn ($choice) => $choice->label(), // Assure-toi que label() existe dans StatutCommande
                'placeholder' => 'Sélectionner un statut',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livraison::class,
        ]);
    }
}
