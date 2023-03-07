<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    //Function which allows the user to show his/her profile
    #[Route('show/{id}', name: 'showProfile', requirements: ['id' => '\d+'])]
    public function showProfile(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        dump($user);

        if(!$user){
            //throws 404 if user doesn't exist
            throw $this->createNotFoundException("Oops ! Utilisateur inconnu !");
        }

        return $this->render('/user/show.html.twig',[
          'user' => $user
        ]);
    }

    //Creation of a new User to test show function





}
