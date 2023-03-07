<?php

namespace App\Form;

use App\Entity\Activity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('startingDateTime', DateTimeType::class, [
                'label' => 'Date et heure de la sortie : ',
                'html5' => true,
                'widget' => 'single_text'
            ] )
            ->add('duration', NumberType::class, [
                'label' => 'Durée : '
            ])
            ->add('registrationDeadLine', DateType::class, [
                'label' => 'Date limite d\'inscription : ',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('maxRegistrationNb', NumberType::class, [
                'label' => 'Nombre de place : ',
                "post_max_size_message" => '30'
            ])
            ->add('overview', TextareaType::class, [
                'label' => 'Description : ',
                'required' => false,
                'attr' => [
                        'class' => "row"
                ]
            ])
            ->add('location', TextType::class)
            ->add('campus', ChoiceType::class, [
                'choices' => [
                            'Quimper' => "quimper",
                            'Rennes' => "rennes",
                            'Niort' => "niort",
                            'Nantes' => "nantes"
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
            'required' => false
        ]);
    }
}
