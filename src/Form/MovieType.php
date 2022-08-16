<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Movie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre du film",
            ])
            ->add('type', ChoiceType::class, [
                'placeholder' => 'Choisissez un film ou une série',
                'choices' => [
                    'Film 🙃' => 'film', 
                    'Série 😀' => 'série'
                ],
                'expanded' => true,
            ])
            ->add('releaseDate', DateTimeType::class, [
                //'years' => range(1895, date('Y') + 10),
                'label' => 'Vous avez vu ce film le...',
                'widget' => 'single_text',
                'input' => 'datetime',
            ])
            ->add('duration', null, [
                'help' => 'Durée en minutes',
                'label' => 'Durée du film'
            ])
            ->add('summary', null, [
                'help' => 'Accroche',
            ])
            ->add('synopsis', null, [
                'help' => 'Résumé',
            ])
            ->add('poster', UrlType::class, [
                'help' => 'Url de l\'image'
            ])
            ->add('rating', ChoiceType::class, [
                'placeholder' => 'Choisissez une note entre 1 et 5',
                'choices' => [
                    '1' => 1, 
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ])
            ->add('genres', EntityType::class, [
                'label' => 'Choisir le ou les genres du média',
                'choice_label' => 'name',
                'class' => Genre::class,
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
