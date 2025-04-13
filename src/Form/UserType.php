<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('forename')
            ->add('pseudo')
            ->add('profile_pic', FileType::class, [
                'label' => 'Profile Picture',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File ([
                        'maxSize' => '5000k',
                        'mimeTypes' => ['image/jpeg','image/png','image/webp'],
                        'mimeTypesMessage' => 'Please upload a valid Image File (jpg, jpeg, png, webp)',
                        'maxSizeMessage' => 'The image file is too large . Allowed size is 5MB',
                        ])
                    ],
                ])
            ->add('banner', FileType::class, [
                'label' => 'Banner',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File ([
                        'maxSize' => '5000k',
                        'mimeTypes' => ['image/jpeg','image/png','image/webp'],
                        'mimeTypesMessage' => 'Please upload a valid Image File (jpg, jpeg, png, webp)',
                        'maxSizeMessage' => 'The image file is too large . Allowed size is 5MB',
                        ])
                    ],
                ])
            ->add('country')
            ->add('city')
            ->add('biography')
            ->add('submit', SubmitType::class,[
                'attr' => [
                    'class' => 'btn btn-success',
                    'value' => 'Modifier',
                    'type' => 'submit',
                    'id' => 'submit',
                    'name' => 'submit',
                ]
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
