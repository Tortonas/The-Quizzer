<?php

namespace App\Controller\Crud;

use App\Entity\FreqAskedQuestion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FreqAskedQuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FreqAskedQuestion::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('question'),
            TextField::new('answer'),
        ];
    }
}
