<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\QuestionExtra;
use App\Enum\Quiz\LevelEnum;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
            TextField::new('path'),
            ChoiceField::new('supportingContentTypeEnum')
        ];
    }
}
