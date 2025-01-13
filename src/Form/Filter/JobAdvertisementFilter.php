<?php

declare(strict_types=1);

namespace App\Form\Filter;

use App\DataTransferObject\JobFilterDto;
use App\Enum\Job\EmploymentTypeEnum;
use App\Enum\Job\PaymentFrequencyEnum;
use App\Form\Type\SalaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobAdvertisementFilter extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod(Request::METHOD_GET)
            ->add('keyword', TextType::class, [
                'required' => false,
                'label' => 'keyword',
                'attr' => [
                    'placeholder' => 'keyword',
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
                    'data-action' => 'keyup->format-number#format'
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('currency', CurrencyType::class, [
                'required' => false,
                'label' => 'Currency',
                'preferred_choices' => ['USD', 'EUR', 'CNY'],
                'attr' => [
                    'placeholder' => 'Currency',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'autocomplete' => true
            ])
            ->add('paymentFrequencyEnum', EnumType::class, [
                'required' => false,
                'class' => PaymentFrequencyEnum::class,
                'choice_label' => 'value',
                'label' => 'Payment Frequency',
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'autocomplete' => true
            ])
            ->add('employmentTypeEnum', EnumType::class, [
                'required' => false,
                'class' => EmploymentTypeEnum::class,
                'choice_label' => 'value',
                'label' => false,
                'attr' => [
                    'placeholder' => 'Employment Type',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'autocomplete' => true
            ])
            ->add('country', CountryType::class, [
                'required' => false,
                'label' => 'Country',
                'attr' => [
                    'placeholder' => 'Country',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'preferred_choices' => [
                  'CN', 'JP', 'KR'
                ],
                 'autocomplete' => true
            ])
            ->add('city', TextType::class, [
                'required' => false,
                'label' => 'City',
                'attr' => [
                    'placeholder' => 'City',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobFilterDto::class,
        ]);
    }
}
