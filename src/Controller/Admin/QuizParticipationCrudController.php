<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\QuizParticipation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class QuizParticipationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return QuizParticipation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('owner'),
            AssociationField::new('quiz'),
            CollectionField::new('answers')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ]),
            NumberField::new('score'),
            DateTimeField::new('startAt'),
            DateTimeField::new('completedAt'),

        ];
    }
}
