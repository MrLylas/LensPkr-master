<?php

namespace App\Form;

use App\Entity\Job;
use App\Entity\User;
use App\Entity\Speciality;
use App\Entity\ContractType;
use PhpParser\Builder\Enum_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PostJobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('job_name')
            ->add('description')
            ->add('begin')
            ->add('finish')
            ->add('location')
            ->add('cp')
            ->add('job_speciality', EntityType::class, [
                'class' => Speciality::class,
                'choice_label' => 'speciality_name',
            ])
            ->add('contract', EntityType::class, [
                'class' => ContractType::class,
                'choice_label' => 'name',
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
            'data_class' => Job::class,
        ]);
    }
}
