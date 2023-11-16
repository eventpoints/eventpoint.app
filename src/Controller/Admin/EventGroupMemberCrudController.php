<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\EventGroup\EventGroupMember;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class EventGroupMemberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EventGroupMember::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('owner'),
            AssociationField::new('roles'),
        ];
    }
}
