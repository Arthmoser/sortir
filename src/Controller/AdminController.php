<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\User;
use App\Form\CampusType;
use App\Form\RegistrationFormType;
use App\Repository\CampusRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use function PHPUnit\Framework\throwException;


#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{

    #[Route('/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->remove('profilePicture');
        $form->remove('isAllowed');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setRoles($form->get('roles')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a bien été rentré !');

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/campus', name: 'campus_list')]
    #[Route('/campus/{id}', name: 'update')]
    public function list(CampusRepository $campusRepository, Request $request, int $id = 0): Response
    {
        $pathCampus = '/admin/campus';
        $pathUpdate = '/admin/campus/' . $id;

//        if ($request->getPathInfo() == $pathAdd) {
//        }
        $campus = new Campus();

        $campusForm = $this->createForm(CampusType::class, $campus);

        $campusForm->handleRequest($request);

        if ($campusForm->isSubmitted()) {//TODO isValid condition
        dump($campus);
            $campusRepository->save($campus, true);
            $this->addFlash("success", "campus ajouter ! ");

            return $this->redirectToRoute('admin_campus_list', ['id' => $id]);

        }

        $campuses = $campusRepository->findBy([], ['name' => 'ASC']);
        return $this->render('admin/campus/list.html.twig', [
            'campuses' => $campuses,
            'campusForm' => $campusForm->createView()
        ]);
    }


    #[Route('/campus/remove/{id}', name: 'remove')]
    public function removeCampus(int $id, CampusRepository $campusRepository): Response
    {
        $campuses = $campusRepository->find($id);

        if ($campuses) {
            $campusRepository->remove($campuses, true);
            $this->addFlash("warning", "Le campus a été supprimé ! ");
        } else {
            throw $this->createNotFoundException("Ce campus ne peut pas être supprimé !");
        }
        return $this->redirectToRoute('admin_campus_list');

    }

}
