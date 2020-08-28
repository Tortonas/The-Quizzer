<?php

namespace App\Controller\Crud;

use App\Entity\GlobalNotification;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GlobalNotificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GlobalNotification::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
