<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use EmilePerron\TinymceBundle\Form\Type\TinymceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NewTeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TinymceType::class, [
                'attr' => [
                    "toolbar" => "bold italic underline | bullist numlist",
                    "menubar" => false
                ]
            ])
            ->add('team_pic', FileType::class, [
                'label' => 'Team\'s Picture',
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
            ->add('team_banner', FileType::class, [
                'label' => 'Team\'s Banner',
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
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
