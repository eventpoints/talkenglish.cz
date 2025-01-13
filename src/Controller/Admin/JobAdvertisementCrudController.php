<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\JobAdvertisement;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CurrencyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class JobAdvertisementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return JobAdvertisement::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextareaField::new('description'),
            ChoiceField::new('employmentTypeEnum'),
            CountryField::new('country'),
            TextField::new('city'),
            NumberField::new('salary'),
            CurrencyField::new('currency'),
            ChoiceField::new('paymentFrequencyEnum'),
            DateField::new('createdAt')->onlyOnIndex(),
            DateField::new('publishedAt'),
            BooleanField::new('isRelocationIncluded')->setRequired(false),
        ];
    }
}
