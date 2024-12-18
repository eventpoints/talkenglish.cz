<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Lesson;
use App\Enum\Quiz\CategoryEnum;
use App\Enum\Quiz\LevelEnum;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CurrencyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LessonCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lesson::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextareaField::new('description'),
            TextField::new('onlineUrl'),
            ChoiceField::new('categoryEnum')->setEmptyData(CategoryEnum::GENERAL),
            ChoiceField::new('levelEnum')->setEmptyData(LevelEnum::A1),
            MoneyField::new('price')->setCurrencyPropertyPath('currency')->setStoredAsCents(true),
            CurrencyField::new('currency'),
            DateTimeField::new('startAt')->setTimezone('UTC'),
            DateTimeField::new('endAt')->setTimezone('UTC'),
            AssociationField::new('teacher'),
            AssociationField::new('owner')->onlyOnIndex()
        ];
    }
}
