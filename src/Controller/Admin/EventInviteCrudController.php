<?php

namespace App\Controller\Admin;

use App\Entity\Event\EventInvitation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class EventInviteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EventInvitation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('event'),
            AssociationField::new('owner')
        ];
    }
}
