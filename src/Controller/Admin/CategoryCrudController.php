<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use Doctrine\ORM\Cache\AssociationCacheEntry;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            AssociationField::new('subcategories')
                ->setFormTypeOption(
                    'by_reference', false
                )->autocomplete(),
        ];
    }

}
