<?php

namespace App\Controller\Admin;

use App\Entity\UserSkill;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserSkillCrudController extends AbstractCrudController
{
    // ReadOnlyTrait permet d'appliquer les configuratons définies dans Trait\ReadOnlyTrait.php
    use Trait\ReadOnlyTrait;
    public static function getEntityFqcn(): string
    {
        return UserSkill::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
