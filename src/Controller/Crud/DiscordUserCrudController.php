<?php

namespace App\Controller\Crud;

use App\Entity\DiscordUser;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DiscordUserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DiscordUser::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('discordId'),
            AssociationField::new('user'),
            BooleanField::new('admin')
        ];
    }
}
