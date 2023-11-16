<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Event\Event;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            DateTimeField::new('startAt'),
            DateTimeField::new('endAt'),
            BooleanField::new('isPrivate'),
            AssociationField::new('eventParticipants')->setFormTypeOption(
                'by_reference',
                false
            )->autocomplete(),
            AssociationField::new('categories')->setFormTypeOption(
                'by_reference',
                false
            )->autocomplete(),

        ];
    }
}
