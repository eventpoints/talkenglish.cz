<?php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CurrencyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use pq\Result;

class LessonCrudController extends AbstractCrudController
{

    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Lesson::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextEditorField::new('description'),
            TextField::new('onlineUrl'),
            MoneyField::new('price')->setCurrencyPropertyPath('currency')->setStoredAsCents(true),
            CurrencyField::new('currency'),
            DateTimeField::new('startAt')->setTimezone('UTC'),
            DateTimeField::new('endAt')->setTimezone('UTC'),
            AssociationField::new('teacher'),
            AssociationField::new('owner')->onlyOnIndex()
        ];
    }
}
