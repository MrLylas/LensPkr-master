<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Project;
use App\Form\ImageType;
use App\Entity\ProjectImage;
use Symfony\Component\Form\AbstractType;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProjectImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'label' => 'Add Image',
            ])
            // ->add('image', ImageType::class, [
            //     'label' => 'Add Image',
            // ])
            ->add('submit', SubmitType::class,[
                'label' => 'Add',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectImage::class,
        ]);
    }
}
