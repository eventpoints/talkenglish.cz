<?php

declare(strict_types=1);

namespace App\Form\Form\Quiz;

use App\Entity\Question;
use App\Entity\QuestionExtra;
use App\Enum\Quiz\CategoryEnum;
use App\Enum\Quiz\QuestionTypeEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextType::class, [
                'label' => 'title',
            ])
            ->add('questionTypeEnum', EnumType::class, [
                'class' => QuestionTypeEnum::class
            ])
            ->add('questionTypeEnum', EnumType::class, [
                'class' => QuestionTypeEnum::class,
                'data' => QuestionTypeEnum::MULTIPLE_CHOICE
            ])
            ->add('categoryEnum', EnumType::class, [
                'class' => CategoryEnum::class,
                'data' => CategoryEnum::GENERAL
            ])
            ->add('questionExtra', EntityType::class, [
                'required' => false,
                'class' => QuestionExtra::class,
                'choice_label' => 'name',
                'autocomplete' => true,
            ])
            ->add('answerOptions', CollectionType::class, [
                'entry_type' => AnswerOptionFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
