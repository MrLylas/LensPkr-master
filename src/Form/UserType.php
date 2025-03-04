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
            // ->add('email')
            // ->add('roles')
            // ->add('password')
            ->add('name')
            ->add('forename')
            ->add('pseudo')
            ->add('profile_pic', FileType::class, [
                'label' => 'Profile Picture',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File ([
                        'maxSize' => '3000k',
                        'mimeTypes' => ['image/*',],
                        'mimeTypesMessage' => 'Please upload a valid Image File (jpg, jpeg, png, webp)',
                        ])
                    ],
                ])
            ->add('banner', FileType::class, [
                'label' => 'Banner',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File ([
                        'maxSize' => '3000k',
                        'mimeTypes' => ['image/*',],
                        'mimeTypesMessage' => 'Please upload a valid Image File (jpg, jpeg, png, webp)',
                        ])
                    ],
                ])
            ->add('country')
            ->add('city')
            // ->add('isVerified')
            ->add('biography')
            // ->add('userSkills', CollectionType::class, [
            //     'entry_type' => UserSkillType::class,
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     // 'multiple' => true
            // ])
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
