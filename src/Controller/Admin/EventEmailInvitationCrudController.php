<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Event\EventEmailInvitation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EventEmailInvitationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EventEmailInvitation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            EmailField::new('email'),
            TextField::new('token'),
        ];
    }
}
