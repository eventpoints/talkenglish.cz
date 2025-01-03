<?php

declare(strict_types=1);

namespace App\Form\Form\Quiz;

use App\Entity\Answer;
use App\Entity\AnswerOption;
use App\Entity\Question;
use App\Form\Type\EntitySelectionGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnswerOptionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextType::class, [
                'label' => 'content',
            ])
            ->add('isCorrect', CheckboxType::class, [
                'label' => 'is correct answer?',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AnswerOption::class,
        ]);
    }
}
