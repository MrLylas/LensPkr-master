<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
            ->add('description')
            ->add('submit', SubmitType::class,[
                'label' => 'Ajouter',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->add('team', EntityType::class, [
                'class' => Team::class,
                // Cette ternaire permet de filtrer les équipes de l'utilisateur en fonction de l'utilisateur connecté
                'choices' => $user ? $user->getMyTeams() : [], // On filtre par les équipes de l'utilisateur
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez une équipe',
                'required' => true,
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
