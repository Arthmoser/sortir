<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class UserController extends AbstractController
{

    //Creation of a new password for the connected user
    #[Route('show/{id}', name: 'passwordModification', requirements: ['id' => '\d+'])]
    public function passwordModification(int $id, UserRepository $userRepository,
                                         Request $request, UserPasswordHasherInterface $userPasswordHasher,
                                         UserAuthenticatorInterface $userAuthenticator,
                                         UserAuthenticator $authenticator,
                                         EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);

        if(!$user){
            //throws 404 if user doesn't exist
            throw $this->createNotFoundException("Oops ! Utilisateur inconnu !");
        }

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


                if ($form->isSubmitted() && $form->isValid()) {
                    // encode the plain password
                    $password = $form->get('plainPassword');
                    if($password) {
                        $user->setPassword(
                            $userPasswordHasher->hashPassword(
                                $user,
                                (string)$password
                            )
                        );
                    }

                    dump($user);

                    $entityManager->persist($user);
                    $entityManager->flush();

                    return $userAuthenticator->authenticateUser(
                        $user,
                        $authenticator,
                        $request
                    );
                }

            return $this->render('user/show.html.twig', [
                'registrationForm' => $form->createView(),
                'user' => $user
            ]);
        }
}
