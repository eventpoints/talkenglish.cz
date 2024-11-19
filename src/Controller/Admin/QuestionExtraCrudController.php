<?php

namespace App\Controller\Admin;

use App\Entity\QuestionExtra;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class QuestionExtraCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return QuestionExtra::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextEditorField::new('content'),
        ];
    }
}
