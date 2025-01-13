<?php

declare(strict_types=1);

namespace App\Form\Form\Job;

use App\Entity\JobAdvertisement;
use App\Enum\Job\EmploymentTypeEnum;
use App\Enum\Job\PaymentFrequencyEnum;
use App\Form\Type\SalaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobAdvertisementFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'attr' => [
                    'placeholder' => 'Title',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('salary', SalaryType::class, [
                'required' => false,
                'label' => 'Salary',
                'attr' => [
                    'placeholder' => 'Salary',
                    'data-controller' => 'format-number',
                    'data-format-number-target' => 'input',
                    'data-action' => 'keyup->format-number#format',
                    'autocomplete' => 'off'
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('currency', CurrencyType::class, [
                'label' => 'Currency',
                'preferred_choices' => ['USD', 'EUR', 'CNY'],
                'attr' => [
                    'placeholder' => 'Currency',
                    'autocomplete' => 'off'
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'autocomplete' => true
            ])
            ->add('paymentFrequencyEnum', EnumType::class, [
                'class' => PaymentFrequencyEnum::class,
                'choice_label' => 'value',
                'label' => 'Payment Frequency',
                'attr' => [
                    'autocomplete' => 'off'
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'autocomplete' => true
            ])
            ->add('employmentTypeEnum', EnumType::class, [
                'class' => EmploymentTypeEnum::class,
                'choice_label' => 'value',
                'label' => 'Employment Type',
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'autocomplete' => true,
            ])
            ->add('country', CountryType::class, [
                'required' => false,
                'help' => 'leave blank if job is remote',
                'label' => 'Country',
                'attr'=>[
                    'autocomplete' => 'off'
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'autocomplete' => true
            ])
            ->add('city', TextType::class, [
                'required' => false,
                'label' => 'City',
                'help' => 'leave blank if job is remote',
                'attr' => [
                    'placeholder' => 'City',
                    'autocomplete' => 'off'
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('company', TextType::class, [
                'label' => 'Company name',
                'attr' => [
                    'placeholder' => 'Company name',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('applicationUrl', UrlType::class, [
                'required' => false,
                'label' => 'Application Url',
                'attr' => [
                    'placeholder' => 'Application Url',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('applicationEmailAddress', EmailType::class, [
                'required' => false,
                'label' => 'Application E-mail address',
                'attr' => [
                    'placeholder' => 'Application E-mail address',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('applicationPhoneNumber', TelType::class, [
                'required' => false,
                'label' => 'Application Phone Number',
                'attr' => [
                    'placeholder' => 'Application Phone Number',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobAdvertisement::class,
        ]);
    }
}
