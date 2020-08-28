<?php

namespace App\Controller\Crud;

use App\Entity\FreqAskedQuestion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FreqAskedQuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FreqAskedQuestion::class;
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
