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

            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Mot de passe : ',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ],
            )
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
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
                'constraints' => [
                    new Image([
                            "maxSize" => '5000k',
                            "mimeTypesMessage" => "Image format not allowed !",

                        ]
                    )
                ]
            ])

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
