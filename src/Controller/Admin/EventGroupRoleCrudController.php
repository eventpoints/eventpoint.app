<?php

namespace App\Controller\Admin;

use App\Entity\EventGroup\EventGroupRole;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EventGroupRoleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EventGroupRole::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextField::new('name'),
            AssociationField::new('eventGroupMember'),
        ];
    }
}
