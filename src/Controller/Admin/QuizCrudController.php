<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Quiz;
use App\Form\Form\Quiz\QuestionFormType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class QuizCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Quiz::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextField::new('slug'),
            TextareaField::new('description'),
            NumberField::new('timeLimitInMinutes'),
            ChoiceField::new('categoryEnum'),
            ChoiceField::new('levelEnum'),
            CollectionField::new('questions')
                ->setEntryType(QuestionFormType::class)->setEntryIsComplex(),
            DateField::new('publishedAt')->setRequired(false),
        ];
    }
}
