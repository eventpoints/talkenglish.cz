<?php

namespace App\Controller\Admin;

use App\Entity\Question;
use App\Enum\Quiz\QuestionTypeEnum;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

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
            TextField::new('content'),
            ChoiceField::new('questionTypeEnum'),
            ChoiceField::new('categoryEnum'),
            ChoiceField::new('levelEnum'),
            NumberField::new('timeLimitInSeconds'),
            CollectionField::new('answerOptions')->useEntryCrudForm(AnswerOptionCrudController::class),
        ];
    }
}
