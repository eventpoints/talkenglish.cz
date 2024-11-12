<?php

namespace App\Form\Filter;

use App\DataTransferObject\QuizFilterDto;
use App\Enum\Quiz\LevelEnum;
use App\Enum\Quiz\CategoryEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod(Request::METHOD_GET)
            ->add('keyword', TextType::class, [
                'required' => false,
                'label' => 'Keyword',
                'attr' => [
                    'placeholder' => 'Keyword',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])->add('categoryEnum', EnumType::class, [
                'required' => false,
                'class' => CategoryEnum::class,
                'choice_label' => 'value',
                'label' => 'Quiz Category',
                'attr' => [
                    'placeholder' => 'Quiz Category',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])->add('levelEnum', EnumType::class, [
                'required' => false,
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuizFilterDto::class,
        ]);
    }
}
