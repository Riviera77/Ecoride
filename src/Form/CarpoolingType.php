<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Carpooling;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CarpoolingType extends AbstractType
{
    /* Form for show the results of the search of the carpooling */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        
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
            ->add('price', MoneyType::class, [
                'currency' => 'EUR',
            ])
            ->add('numberSeats', IntegerType::class)
            ->add('preference')
            ->add('status')
            ->add('cars', EntityType::class, [
                'class' => Car::class,
                'query_builder' => function ($er) use ($user) {
                    return $er->createQueryBuilder('c')
                        ->where('c.users = :user')
                        ->setParameter('user', $user);
                },
                'choice_label' => function (Car $car) {
                    return sprintf('%s %s (%s) - %s [%s]',
                        $car->getRegistration(), 
                        $car->getdateFirstRegistration(), 
                        $car->getModel(),
                        $car->getColor(),
                        $car->getMark(),
                        $car->isEnergy() ? 'électrique' : 'thermique'
                    );
                },
                'placeholder' => 'Choisir un véhicule',
                'label' => 'Véhicule',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Carpooling::class,
            'user' => null,
        ]);
    }
}