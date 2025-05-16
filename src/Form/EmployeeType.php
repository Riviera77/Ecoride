<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('username')
            ->add('password')
            /* ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Employé' => 'ROLE_EMPLOYEE',
                ],
                'multiple' => true,
                'expanded' => false,
                'data' => ['ROLE_EMPLOYEE'],
                'label' => false, // ou true si tu veux afficher
                'attr' => ['hidden' => true], // facultatif pour cacher
            ]) */
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Actif' => 'ROLE_EMPLOYEE',
                    'Suspendu' => 'ROLE_EMPLOYEE_SUSPENDED',
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