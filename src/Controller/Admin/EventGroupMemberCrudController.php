<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\EventGroup\EventGroupMember;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class EventGroupMemberCrudController extends AbstractCrudController
{
    #[\Override]
    public static function getEntityFqcn(): string
    {
        return EventGroupMember::class;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('owner'),
            AssociationField::new('roles'),
        ];
    }
}
