<?php

namespace App\Controller\Admin;

use App\Entity\Job;
use DateTime;
use Dom\Text;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\Date;

class JobCrudController extends AbstractCrudController
{
    use Trait\ReadOnlyTrait;
    public static function getEntityFqcn(): string
    {
        return Job::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('job_name'),
            TextEditorField::new('description')->hideOnIndex(),
            DateField::new('begin')->hideOnIndex(),
            DateField::new('finish')->hideOnIndex(),
            TextField::new('location'),
            TextField::new('cp')->hideOnIndex(),
            DateTimeField::new('creation'),
            AssociationField::new('job_speciality')->hideOnIndex(),
            AssociationField::new('contract'),
            AssociationField::new('user')->hideOnIndex(),
        ];
    }

}
