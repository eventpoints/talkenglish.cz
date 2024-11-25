<?php

declare(strict_types=1);

namespace App\Form\Form;

use App\Entity\User;
use App\Enum\Quiz\LevelEnum;
use App\Exception\ShouldNotHappenException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserAccountFormType extends AbstractType
{


    public function __construct(
        private readonly Security $security,
        private readonly TranslatorInterface $translator
    )
    {
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $this->security->getUser();
        if(!$currentUser instanceof User){
            throw new ShouldNotHappenException(message: 'The user should be logged in by now.');
        }

        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
                'attr' => [
                    'placeholder' => 'First Name',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
                'attr' => [
                    'placeholder' => 'Last Name',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail Address',
                'attr' => [
                    'placeholder' => 'E-mail Address',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('timezone', TimezoneType::class, [
                'label' => 'timezone',
                'attr' => [
                    'placeholder' => 'timezone',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'autocomplete' => true
            ])
            ->add('levelEnum', EnumType::class, [
                'disabled' => true,
                'required' => false,
                'class' => LevelEnum::class,
                'choice_label' => function (LevelEnum $levelEnum): string {
                    return $levelEnum->name . " - " . $levelEnum->value;
                },
                'help' => $this->translator->trans('level-assessment-next-available', ['date' => $currentUser->getLevelAssessmentQuizTakenAt()->addMonths(3)->toFormattedDateString()]),
                'label' => 'Level',
                'attr' => [
                    'placeholder' => 'Level',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
