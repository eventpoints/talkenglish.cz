<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\AnswerOption;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AnswerOptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AnswerOption::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('question')->onlyOnIndex(),
            TextField::new('content'),
            BooleanField::new('isCorrect')->setRequired(false),
        ];
    }
}
