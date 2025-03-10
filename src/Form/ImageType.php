<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', FileType::class, [
            'label' => 'Your File',
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File ([
                    'maxSize' => '5000k',
                    'mimeTypes' => ['image/*',],
                    'mimeTypesMessage' => 'Please upload a valid Image File (jpg, jpeg, png, webp)',
                    ])
                ],
            ])
            ->add('description')
            // ->add('created_at', null, [
            //     'widget' => 'single_text',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
