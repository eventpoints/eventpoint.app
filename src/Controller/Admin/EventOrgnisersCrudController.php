<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Event\EventOrganiser;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class EventOrgnisersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EventOrganiser::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('owner'),
            AssociationField::new('event'),
            AssociationField::new('roles')->setFormTypeOption(
                'by_reference',
                false
            ),
        ];
    }
}
