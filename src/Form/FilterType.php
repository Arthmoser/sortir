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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class,[
                'class' => Campus::class,
                'choice_label' => function ($campus) {
                return $campus->getName();
                },
                'empty_data' => null,
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
                'attr' => [
                    'id' => 'isRegistered'
                ]
            ])

            ->add('isNotRegistered', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit.e : ',
                'attr' => [
                    'id' => 'isNotRegistered'
                ]
            ])

            ->add('availableActivity', CheckboxType::class, [
                'label' => 'Sorties passées : ',
            ])

            // 3. Add 1 event listeners for the form
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {


//                $filter = $event->getData();
//                $form = $event->getForm();
//
//                if (!$filter)
//                {
//                    return;
//                }
//
//                if (isset($filter['isRegistered']))
//                {
//                    unset($filter['isNotRegistered']);
//                } elseif (isset($filter['isNotRegistered']))
//                {
//                    unset($filter['isRegistered']);
//                }
            });

    }

    public function onPreSetData(FormEvent $event): void
    {

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FilterModel::class,
            'required' => false
        ]);
    }
}
