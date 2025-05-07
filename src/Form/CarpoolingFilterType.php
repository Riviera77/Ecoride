<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarpoolingFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('maxDuration', IntegerType::class, [
                'required' => false,
                'label' => 'Durée max (en heures)',
            ])
            ->add('ecological', CheckboxType::class, [
                'required' => false,
                'label' => 'Uniquement électrique',
            ])
            ->add('maxPrice', IntegerType::class, [
                'required' => false,
                'label' => 'Prix maximum (€)',
            ])
            ->add('minRating', IntegerType::class, [
                'required' => false,
                'label' => 'Note minimum',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}