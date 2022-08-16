<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('content', TextareaType::class)
            ->add('rating', ChoiceType::class, [
                // @link https://symfony.com/doc/current/reference/forms/types/choice.html#choices
                'choices' => [
                    'Excellent' => 5,
                    'Très bon' => 4,
                    'Bon' => 3,
                    'Peut mieux faire' => 2,
                    'A éviter' => 1
                ],
                'placeholder' => 'Votre appréciation...',
            ])
            ->add('reactions', ChoiceType::class, [
                'placeholder' => 'Choisissez une réaction',
                'choices' => [
                    'Rire' => 'Rire', 
                    'Pleurer' => 'Pleurer', 
                    'Réfléchir' => 'Réfléchir', 
                    'Dormir' => 'Dormir', 
                    'Rêver' => 'Rêver' 
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => false
                ])
            ->add('watchedAt', DateTimeType::class, [
                'label' => 'Vous avez vu ce film le...',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            //->add('movie', null, ['placeholder' => 'Choississez un film'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
