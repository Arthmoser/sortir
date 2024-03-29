<?php

namespace App\Form;

use App\Entity\Campus;
use App\Form\Model\FilterModel;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class,[
                'class' => Campus::class,
                'placeholder' => '-Tous les campus-',
                'choice_label' => function ($campus) {
                return $campus->getName();
                },
                'empty_data' => '',
                'label' => 'Campus : ',
                'query_builder' => function (CampusRepository $campusRepository) {
                    $qb = $campusRepository->createQueryBuilder("c");
                    $qb->addOrderBy("c.name");
                    return $qb;
                }
            ])

            ->add('search', SearchType::class,[
                'empty_data' => null,
                'trim' => true,
                'label' => 'Le nom de la sortie contient : '
            ])

            ->add('startingDateTime', DateType::class, [
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
                'label' => 'Sorties auxquelles je suis inscrit.e : '
            ])

            ->add('isNotRegistered', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit.e : '
            ])

            ->add('availableActivity', CheckboxType::class, [
                'label' => 'Sorties passées : ',
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
