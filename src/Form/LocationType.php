<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Location;
use App\Repository\CityRepository;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\ORM\Mapping\Entity;
use Faker\Provider\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du lieu'
            ])
            ->add('street', TextType::class, [
                    'label' => 'Rue'
                ])
            ->add('latitude', TextType::class, [
                    'label' => 'Latitude (optionnel)'
            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude (optionnel)'
            ])
            ->add('city',EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'Ville',
                'query_builder' => function(CityRepository $cityRepository){
                $qb = $cityRepository->createQueryBuilder(("c"));
                $qb->addOrderBy("c.name");
                return $qb;
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
            'required' => false
        ]);
    }
}
