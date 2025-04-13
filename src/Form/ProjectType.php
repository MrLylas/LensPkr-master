<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\Project;
use App\Entity\Speciality;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use EmilePerron\TinymceBundle\Form\Type\TinymceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProjectType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $user = $this->security->getUser();

        $builder
            ->add('projectName')
            ->add('description', TinymceType::class, [
                'attr' => [
                    "toolbar" => "bold italic underline | bullist numlist",
                    "menubar" => false
                ]
            ])
            ->add('cover', FileType::class, [
                'label' => 'Chose a cover for your project',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5000k',
                        'mimeTypes' => ['image/jpeg','image/png','image/webp'],
                        'mimeTypesMessage' => 'Please upload a valid image file'
                    ])
                ]
            ])
            ->add('team', EntityType::class, [
                'class' => Team::class,
                'label' => 'Team associated to this project',
                // Cette ternaire permet de filtrer les équipes de l'utilisateur en fonction de l'utilisateur connecté
                'choices' => $user ? $user->getMyTeams() : [], // On filtre par les équipes de l'utilisateur
                'choice_label' => 'name',
                'placeholder' => 'Select a team',
                'required' => false,
            ])
            ->add('speciality', EntityType::class, [
                'class' => Speciality::class,
                'label' => 'Speciality associated to this project',
                'choice_label' => 'speciality_name',
                'placeholder' => 'Select a speciality',
                'required' => false,
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Ajouter',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
