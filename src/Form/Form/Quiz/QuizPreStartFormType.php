<?php

namespace App\Form\Form\Quiz;

use App\DataTransferObject\QuizConfigurationDto;
use App\Enum\Quiz\LevelEnum;
use App\Enum\Quiz\CategoryEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizPreStartFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('questionCategoryEnum', EnumType::class, [
                'class' => CategoryEnum::class,
                'choice_label' => 'value',
                'label' => 'Quiz Category',
                'attr' => [
                    'placeholder' => 'Quiz Category',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])->add('questionLevelEnum', EnumType::class, [
                'class' => LevelEnum::class,
                'choice_label' => function (LevelEnum $levelEnum): string {
                    return $levelEnum->name . " - " . $levelEnum->value;
                },
                'label' => 'Quiz Category',
                'attr' => [
                    'placeholder' => 'Quiz Category',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('isTimed', CheckboxType::class, [
                'required' => false,
                'label' => 'enable time constraint',
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuizConfigurationDto::class,
        ]);
    }
}
