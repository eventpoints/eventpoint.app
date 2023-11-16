<?php

namespace App\Controller\Admin;

use App\Entity\Event\EventParticipant;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class EventParticipantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EventParticipant::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('owner'),
            DateField::new('createdAt'),
        ];
    }
}
