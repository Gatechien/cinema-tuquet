<?php

namespace App\Form;

use App\Entity\Casting;
use App\Entity\Movie;
use App\Entity\Person;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CastingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role', TextType::class)
            ->add('creditOrder',IntegerType::class, [
                'help' => "Ordre d'apparatition à l'affiche"
            ])
            ->add('person', EntityType::class, [
                'label' => 'Choisissez l\'acteur ou l\'actrice',
                'choice_label' => 'getCompletName',
                'class' => Person::class,
                'multiple' => false,
                'expanded' => false,
                'required' => false
            ])
            ->add('movie', EntityType::class, [
                'label' => 'Choisissez le film ou la série',
                'choice_label' => 'title',
                'class' => Movie::class,
                'multiple' => false,
                'expanded' => true,
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Casting::class,
        ]);
    }
}
