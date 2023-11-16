<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Event\EventRole;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EventRoleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EventRole::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextField::new('name'),
            AssociationField::new('eventParticipant'),
        ];
    }
}
