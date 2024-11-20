<?php

declare(strict_types=1);

namespace App\Form\Filter;

use App\DataTransferObject\LessonFilterDto;
use App\Enum\Quiz\LevelEnum;
use App\Enum\Quiz\CategoryEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class LessonFilterType extends AbstractType
{

    public function __construct(
        private readonly TranslatorInterface $translator
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod(Request::METHOD_GET)
            ->add('categoryEnum', EnumType::class, [
                'required' => false,
                'class' => CategoryEnum::class,
                'choice_label' => 'value',
                'label' => 'Category',
                'attr' => [
                    'placeholder' => 'Category',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'autocomplete' => true
            ])->add('levelEnum', EnumType::class, [
                'required' => false,
                'class' => LevelEnum::class,
                'choice_label' => function (LevelEnum $levelEnum): string {
                    return $levelEnum->name . " - " . $this->translator->trans($levelEnum->value);
                },
                'label' => 'Level',
                'attr' => [
                    'placeholder' => 'Level',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'autocomplete' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LessonFilterDto::class,
        ]);
    }
}
