<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
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
            ]);
    } 

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}