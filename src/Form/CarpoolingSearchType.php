<?php

namespace App\Form;

use Dom\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class CarpoolingSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('departureAddress', TextType::class, [
                'label' => 'Ville de départ',
                'required' => true,
            ])
            ->add('arrivalAddress', TextType::class, [
                'label' => 'Ville d\'arrivée',
                'required' => true,
            ])
            ->add('departureDate', DateType::class, [
                //widget = single_text pour afficher un champ de date 
                //en un seul champ HTML par symfony
                'widget' => 'single_text',
                'label' => 'Date de départ',
                'required' => true,
            ])
            /* ->add('minPrice', NumberType::class, [
                'required' => false,
                'label' => 'Prix min (€)',
                'attr' => ['placeholder' => 'Prix min.']
            ])
            ->add('maxPrice', NumberType::class, [
                'required' => false,
                'label' => 'Prix max (€)',
                'attr' => ['placeholder' => 'Prix max.']
            ]) */
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false, // No need because Form in GET
        ]);
    }
}