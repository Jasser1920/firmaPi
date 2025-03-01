<?php

namespace App\Form;

use App\Entity\Livraison;
use App\Repository\LivraisonRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('paiement', ChoiceType::class, [
                'label' => 'Méthode de paiement',
                'choices' => [
                    'Carte de crédit' => 'credit_card',
                    'PayPal' => 'paypal',
                    'Virement bancaire' => 'bank_transfer',
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('livraison', EntityType::class, [
                'label' => 'Société de livraison',
                'class' => Livraison::class,
                'choice_label' => 'nom_societe',
                'query_builder' => function (LivraisonRepository $repo) {
                    return $repo->createQueryBuilder('l')->orderBy('l.nom_societe', 'ASC');
                },
                'attr' => ['class' => 'form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Confirmer la commande',
                'attr' => ['class' => 'btn btn-checkout mt-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}