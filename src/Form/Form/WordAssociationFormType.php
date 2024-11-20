<?php

declare(strict_types=1);

namespace App\Form\Form;

use App\DataTransferObject\WordAssociationDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WordAssociationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('potentiallyRelatedWord', TextType::class, [
                'label' => 'Give me a related word',
                'attr' => [
                    'placeholder' => 'Give me a related word',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WordAssociationDto::class,
        ]);
    }
}
