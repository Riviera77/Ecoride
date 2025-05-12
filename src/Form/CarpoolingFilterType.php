<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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
            ])
            /* ->add('filter', SubmitType::class, [
            'label' => 'Filtrer',
            'attr' => ['class' => 'btn btn-primary']
        ]) */;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }

    /* give an unique name to the form in html and in the url */
    /* This allows symfony to link the data submitted to the right form */
        public function getBlockPrefix(): string
    {
        return 'carpooling_filter';
    }
}