<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('firstName'),
            TextField::new('lastName'),
            EmailField::new('email'),
            ChoiceField::new('roles')->setChoices([
                'student' => 'ROLE_STUDENT',
                'admin' => 'ROLE_ADMIN',
                'teacher' => 'ROLE_TEACHER',
            ])->allowMultipleChoices()
        ];
    }
}
