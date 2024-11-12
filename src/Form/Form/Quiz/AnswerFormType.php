<?php

namespace App\Form\Form\Quiz;

use App\Entity\Answer;
use App\Entity\AnswerOption;
use App\Entity\Question;
use App\Form\Type\EntitySelectionGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnswerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var Question $question
         */
        $question = $options['question'];
        $builder
            ->add('answers', EntitySelectionGroupType::class, [
                'class' => AnswerOption::class,
                'choices' => $question->getAnswerOptions(),
                'choice_label' => 'content',
                'expanded' => true,
                'multiple' => true,
                'row_attr' => [
                    'class' => 'm-0'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
            'question' => Question::class
        ]);
    }
}
