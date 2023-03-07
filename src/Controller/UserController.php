<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Utils\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/show', name: 'user_')]
class UserController extends AbstractController
{

    //Creation of a new password for the connected user
    #[Route('/{id}', name: 'profil', requirements: ['id' => '\d+'])]
    public function passwordModification(int $id, UserRepository $userRepository,
                                         Request $request, Uploader $uploader,
                                         UserPasswordHasherInterface $userPasswordHasher,
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

                    //upload photo
                    /**
                     * @var UploadedFile $file
                     */

                    $file = $form->get('profilePicture')->getData();

                    $newFileName = $uploader->upload(
                      $file,
                      $this->getParameter('upload_user_picture'),
                      $user->getNickname() );

                    $user->setProfilePicture($newFileName);

                    $password = $form->get('password')->getData();

                    if($password) {
                        // encode the plain password
                        $user->setPassword(
                            $userPasswordHasher->hashPassword(
                                $user,
                                $password
                            )
                        );
                    }

                    dump($user);

                    $entityManager->persist($user);
                    $entityManager->flush();

                }

            return $this->render('user/show.html.twig', [
                'registrationForm' => $form->createView(),
                'user' => $user
            ]);
        }
}
