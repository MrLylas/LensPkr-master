<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use EmilePerron\TinymceBundle\Form\Type\TinymceType;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProjectImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $builder
            // ->add('images', FileType::class, [
            //     'label' => 'Ajouter des images',
            //     'multiple' => true,
            //     'mapped' => false,
            //     'required' => true,
            //     'constraints' => [
            //         new NotBlank([
            //             'message' => 'Veuillez sélectionner au moins une image.',
            //         ]),
            //         new File([
            //             'maxSize' => '8000k', //Volontairement exessif
            //             'mimeTypes' => ['image/*'], // Accepte toutes les images volontairement pour les tests
            //             'mimeTypesMessage' => 'Merci d\'uploader une image valide (JPEG, JPG, PNG, WEBP)',
            //         ]),
            //     ],
            // ])


            $builder
                ->add('images', FileType::class, [
                    'label' => 'Ajouter des images',
                    'multiple' => true,
                    'mapped' => false,
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez sélectionner au moins une image.',
                        ]),
                        new All([ // Valide chaque fichier individuellement
                            new File([
                                'maxSize' => '5M',
                                'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                                'mimeTypesMessage' => 'Merci d\'uploader une image valide (JPEG, PNG, WEBP)',
                            ])
                        ])
                    ],
                ])
                ->add('description', TinymceType::class, [
                    'attr' => [
                        "toolbar" => "bold italic underline | bullist numlist",
                        "menubar" => false
                    ]
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Ajouter',
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ],
                ]);
                
 
            // ->add('submit', SubmitType::class, [
            //     'label' => 'Add Pics',
            //     'attr' => [
            //         'class' => 'btn'
            //     ]
            // ])
            // ->add('description', ImageType::class, [
            //     'label' => 'Add description'
            // ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
 