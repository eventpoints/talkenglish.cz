<?php

namespace App\Form\Form\Quiz;

use App\DataTransferObject\QuizConfigurationDto;
use App\Entity\Answer;
use App\Entity\AnswerOption;
use App\Entity\Question;
use App\Entity\QuizParticipation;
use App\Enum\Quiz\LevelEnum;
use App\Enum\Quiz\CategoryEnum;
use App\Form\Type\EntitySelectionGroupType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
