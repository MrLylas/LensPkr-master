<?php

namespace App\Controller\Admin;

use App\Entity\Team;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TeamCrudController extends AbstractCrudController
{
    use Trait\ReadOnlyTrait;
    public static function getEntityFqcn(): string
    {
        return Team::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('name'),
            TextField::new('description'),
            AssociationField::new('membership')//Affiche le nombre de membres de l'équipe seulement sur la page d'index
                ->onlyOnIndex(),
            ArrayField::new('membership')//Affiche les membres de l'équipe seulement sur la page de détails
                ->onlyOnDetail(),
        ];
    }
}
