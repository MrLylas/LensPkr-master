<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProjectFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('filter', ChoiceType::class, [
                'choices' => [
                    'Recents' => 'recent',
                    'A-Z' => 'a-z',
                    'Z-A' => 'z-a',
                    'Popularity' => 'popularity',
                ],
                'required' => false,
                'placeholder' => 'Select a filter',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
