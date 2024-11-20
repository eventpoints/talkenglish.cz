<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Question;
use App\Enum\Quiz\CategoryEnum;
use App\Enum\Quiz\LevelEnum;
use App\Enum\Quiz\QuestionTypeEnum;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class QuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Question::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextEditorField::new('content'),
            ChoiceField::new('questionTypeEnum')->setEmptyData(QuestionTypeEnum::FILL_IN_THE_BLACK),
            ChoiceField::new('categoryEnum')->setEmptyData(CategoryEnum::GENERAL),
            ChoiceField::new('levelEnum')->setEmptyData(LevelEnum::A1),
            NumberField::new('timeLimitInSeconds')->setEmptyData(60),
            CollectionField::new('answerOptions')->useEntryCrudForm(AnswerOptionCrudController::class),
            AssociationField::new('questionExtra')->setRequired(false),
        ];
    }
}
