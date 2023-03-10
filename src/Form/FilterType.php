<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\City;
use App\Form\Model\FilterModel;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use PHPUnit\Util\Filter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class,[
                'class' => Campus::class,
                'choice_label' => 'name',
                'label' => 'Campus : ',
                'query_builder' => function (CampusRepository $campusRepository) {
                    $qb = $campusRepository->createQueryBuilder("c");
                    $qb->addOrderBy("c.name");
                    return $qb;
                }
            ])

            ->add('activity', SearchType::class,[
                'empty_data' => ' ',
                'mapped' => false,
                'trim' => true,
                'label' => 'Le nom de la sortie contient : '
            ])

            ->add('startingDateTime', DateTimeType::class, [
                'label' => 'Entre : ',
                'html5' => true,
                'widget' => 'single_text'
            ])

            ->add('endingDateTime', DateType::class, [
                'label' => 'et : ',
                'html5' => true,
                'widget' => 'single_text'
            ])

            ->add('isOrganiser', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur.trice : ',
            ])

            ->add('isRegistered', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit.e : ',
            ])

            ->add('isNotRegistered', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suispas inscrit.e : ',
            ])

            ->add('availableActivity', CheckboxType::class, [
                'label' => 'Sorties passées : ',
            ])

        ->add('search', SubmitType::class, [
        'label' => 'Rechercher'
    ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FilterModel::class,
            'required' => false
        ]);
    }
}
