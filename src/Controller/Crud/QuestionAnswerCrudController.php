<?php

namespace App\Controller\Crud;

use App\Entity\QuestionAnswer;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class QuestionAnswerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return QuestionAnswer::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('timeAnswered'),
            TextField::new('username'),
            AssociationField::new('user'),
            AssociationField::new('question')
        ];
    }
}
