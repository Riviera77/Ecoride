<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Carpooling;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarpoolingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('departureAddress')
            ->add('arrivalAddress')
            ->add('departureDate', null, [
                'widget' => 'single_text',
            ])
            ->add('arrivalDate', null, [
                'widget' => 'single_text',
            ])
            ->add('departureTime', null, [
                'widget' => 'single_text',
            ])
            ->add('arrivalTime', null, [
                'widget' => 'single_text',
            ])
            ->add('price')
            ->add('numberSeats')
            ->add('preference')
            ->add('status')
            ->add('cars', EntityType::class, [
                'class' => Car::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Carpooling::class,
        ]);
    }
}
