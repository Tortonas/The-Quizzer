<?php

namespace App\Controller\Crud;

use App\Entity\GlobalNotification;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GlobalNotificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GlobalNotification::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('text'),
            DateTimeField::new('endDate'),
        ];
    }
}
