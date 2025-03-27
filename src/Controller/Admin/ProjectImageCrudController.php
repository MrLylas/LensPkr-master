<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Entity\ProjectImage;
use Doctrine\Common\Collections\Collection;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class ProjectImageCrudController extends AbstractCrudController
{
    use Trait\ReadOnlyTrait;
    public static function getEntityFqcn(): string
    {
        return ProjectImage::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            ImageField::new('image'),
            TextField::new('project'),
        ];
    }
}
