<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Image\ImageCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class ImageCollectionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ImageCollection::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('images'),
            DateTimeField::new('createdAt'),
        ];
    }
}
