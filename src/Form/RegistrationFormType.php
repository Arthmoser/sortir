<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\CallbackTransformer;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nickname', TextType::class, [
                'label' => 'Pseudo : '
            ])
            ->add('lastname',TextType::class, [
                'label' => 'Nom : '
            ])
            ->add('firstname',TextType::class, [
                'label' => 'Prénom : '
            ])
            ->add('email', TextType::class, [
                'label' => 'Email : '
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone : '
            ])

            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Attention le mot de passe doit être identique .',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'first_options'  => ['label' => 'Mot de passe : '],
                'second_options' => ['label' => 'confirmation : '],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],

            ])

            ->add('isAllowed', ChoiceType::class, [
                'label'    => 'Utilisateur autorisé ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
                'required' => true,
            ])

            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'label' => 'Campus : ',
                'query_builder' => function(CampusRepository $campusRepository){
                    $qb = $campusRepository->createQueryBuilder("c");
                    $qb->addOrderBy("c.name");
                    return $qb;
                }
            ])

            ->add('profilePicture', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                            "maxSize" => '5000k',
                            "mimeTypesMessage" => "Image format not allowed !",

                        ]
                    )
                ]
            ])

            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm()->getConfig()->getRequestHandler();
//                dd($form);
            })

            ->add('roles', ChoiceType::class, [
            'label'    => 'Niveau d\'acréditation : ',
            'multiple' => true,
            'expanded' => false,
            'choices' => [
                'Admin' => 'ROLE_ADMIN',
                'User' => 'ROLE_USER'
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
