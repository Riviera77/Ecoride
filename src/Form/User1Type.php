<?php

namespace App\Form;

use App\Entity\User;
use App\Form\CarType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class User1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('username')
            ->add('photo')
            ->add('roleType', ChoiceType::class, [
                'label' => 'Je suis :',
                'choices' => [
                    'Chauffeur' => 'chauffeur',
                    'Passager' => 'passager',
                ],
                'expanded' => true,   // box to check
                'multiple' => true,   // multiple choice
                'required' => true,   // required if needed
            ])
            ->add('cars', CollectionType::class, [
                'entry_type' => CarType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Mes véhicules'
            ])
            ->add('preference', TextareaType::class, [
                'required' => false,
                'label' => 'Vos préférences (animaux, musique, discussions, etc.)',
            ])

            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Actif' => 'ROLE_USER',
                    'Suspendu' => 'ROLE_USER_SUSPENDED',
                ],
                'expanded' => false,
                'multiple' => false, // Un seul rôle à la fois
                'label' => 'Statut du compte',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}