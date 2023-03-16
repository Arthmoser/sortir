<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\User;
use App\Form\CampusType;
use App\Form\CityType;
use App\Form\FilterType;
use App\Form\Model\FilterModel;
use App\Form\RegistrationFormType;
use App\Repository\CampusRepository;
use App\Repository\UserRepository;
use App\Repository\CityRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;


#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{

    #[Route('/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->remove('profilePicture');
        $form->remove('isAllowed');
        $form->remove('roles');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a bien été créé !');

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/campus', name: 'campus_list')]
    #[Route('/campus/{id}', name: 'campus_update')]
    public function list(CampusRepository $campusRepository, Request $request, int $id = 0): Response
    {
        $campus1 = new Campus();
        $campus2 = new Campus();
        $isUpdate = false;
        $campuses = $campusRepository->findBy([], ['name' => 'ASC']);

        foreach ($campuses as $camp) {
            if ($camp->getId() == $id) {
                $campus2 = $camp;
            }
        }

        $campusForm = $this->createForm(CampusType::class, $campus1);
        $campusForm2 = $this->createForm(CampusType::class, $campus2);
        $campusForm->handleRequest($request);
        $campusForm2->handleRequest($request);


        if ($request->getPathInfo() == '/admin/campus/' . $id && !$campusForm2->isSubmitted()) {
            $isUpdate = true;

            return $this->render('admin/campus/list.html.twig', [
                'campuses' => $campuses,
                'campusForm' => $campusForm->createView(),
                'campusForm2' => $campusForm2->createView(),
                'isUpdate' => $isUpdate,
                'id' => $id
            ]);
        }


        if ($campusForm2->isSubmitted()) { //TODO condition validation
            $campusRepository->save($campus2, true);
            $this->addFlash("success", "Le campus a bien été modifié ! ");
            $campus2 = $campusForm->getData();
            return $this->redirectToRoute('admin_campus_list', ['id' => $id]);
        }

        if ($campusForm->isSubmitted() && $campusForm->isValid()) {
            $campusRepository->save($campus1, true);
            $this->addFlash("success", "Le campus a bien été ajouté ! ");
            return $this->redirectToRoute('admin_campus_list', ['id' => $id]);
        }

        return $this->render('admin/campus/list.html.twig', [
            'campuses' => $campuses,
            'campusForm' => $campusForm->createView(),
            'isUpdate' => $isUpdate
        ]);
    }


    #[Route('/campus/remove/{id}', name: 'campus_remove')]
    public function removeCampus(int $id, CampusRepository $campusRepository): Response
    {
        $campuses = $campusRepository->find($id);

        if ($campuses) {
            $campusRepository->remove($campuses, true);
            $this->addFlash("warning", "Le campus a bien été supprimé ! ");
        } else {
            throw $this->createAccessDeniedException("Ce campus ne peut pas être supprimé !");
        }
        return $this->redirectToRoute('admin_campus_list');

    }


    #[Route('/city/display/', name: 'city_display')]
    #[Route('/city/update{id}', name: 'city_update')]
    public function displayCity(CityRepository $cityRepository, Request $request, int $id = 0): Response
    {
        $city1 = new City();
        $city2 = new City();

        $filterModel = new FilterModel();
        $cities = [];

        $filterForm = $this->createForm(FilterType::class, $filterModel);
        $filterForm->remove('campus');
        $filterForm->remove('startingDateTime');
        $filterForm->remove('endingDateTime');
        $filterForm->remove('isOrganiser');
        $filterForm->remove('isRegistered');
        $filterForm->remove('isNotRegistered');
        $filterForm->remove('availableActivity');
        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid())
        {

            $cities = $cityRepository->filterCities($filterModel);

        }
        else{
            $cities = $cityRepository->findBy([], ['name' => 'ASC']);
        }


        $isUpdate = false;


        foreach ($cities as $city) {
            if ($city->getId() == $id) {
                $city2 = $city;
                dump($city2);
            }
        }

        $cityForm = $this->createForm(CityType::class, $city1);
        $cityForm2 = $this->createForm(CityType::class, $city2);
        $cityForm->handleRequest($request);
        $cityForm2->handleRequest($request);

        if ($request->getPathInfo() == '/admin/city/update' . $id && !$cityForm2->isSubmitted()) {
            $isUpdate = true;
            return $this->render('/city/city.html.twig', [
                'cities' => $cities,
                'cityForm' => $cityForm->createView(),
                'cityForm2' => $cityForm2->createView(),
                'filterForm' => $filterForm->createView(),
                'city2' => $city2,
                'isUpdate' => $isUpdate,
                'id' => $id
            ]);
        }

        if ($cityForm->isSubmitted() && $cityForm->isValid()) {

            $cityRepository->save($city1, true);
            $this->addFlash('success', 'La ville a bien été ajoutée ! ');

            return $this->redirectToRoute('admin_city_display');
        }

        if ($cityForm2->isSubmitted() && $cityForm2->isValid()) {
            $cityRepository->save($city2, true);
            $this->addFlash("success", "La ville a bien été modifiée ! ");

            return $this->redirectToRoute('admin_city_display');
        }


        return $this->render('city/city.html.twig', [
            'cityForm' => $cityForm->createView(),
            'filterForm' => $filterForm->createView(),
            'cities' => $cities,
            'city1' => $city1,
            'isUpdate' => $isUpdate
        ]);
    }


    #[Route('/city/remove/{id}', name: 'city_remove')]
    public function removeCity(int $id, CityRepository $cityRepository, EntityManagerInterface $entityManager): Response
    {
        $citie = $cityRepository->find($id);
        $citie = $entityManager->getRepository(City::class)->find($id);
        try {
            $entityManager->remove($citie);
            $entityManager->flush();
            $this->addFlash("warning", "La ville a été supprimé ! ");
        } catch (\Exception $e) {
            $this->addFlash("error", 'Cette ville ne peut pas être supprimé ! ');
        }
        return $this->redirectToRoute('admin_city_display');
    }


    #[Route('/user', name: 'userList')]
    public function userList(UserRepository $userRepository): Response
    {

        $users = $userRepository->findBy([], ['lastname' => 'ASC']);
        return $this->render('admin/user/userList.html.twig', [
            'users' => $users
        ]);
    }


    #[Route('/user/remove/{id}', name: 'removeUser')]
    public function removeUser(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        try {
            if ($user) {
                $userRepository->remove($user, true);
                $this->addFlash("success", "L'utilisateur a bien été supprimé !");
            } else {
                $this->addFlash("warning", "La suppression de l'utilisateur a échoué !");
            }
        } catch (\Exception $e) {
            $this->addFlash("warning", "L'utilisateur ne peut pas être supprimé !");
        }
        return $this->redirectToRoute('admin_userList');

    }

    #[Route('/user/disable/{id}', name: 'disableUser')]
    public function disableUser(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if ($user) {

            if ($user->isIsAllowed()) {
                $user->setIsAllowed(false);
                $user->setRoles(['ROLE_PUBLIC']);
                $this->addFlash("success", "Les droits de l'utilisateur ont bien été désactivés !");

            } else {
                $user->setIsAllowed(true);
                $user->setRoles(['ROLE_USER']);
                $this->addFlash("success", "Les droits de l'utilisateur ont bien été réactivés !");
            }
            $userRepository->save($user, true);
        }
        else
        {
            $this->addFlash("warning", "La modification des droits de l'utilisateur a échoué !");
        }
            return $this->redirectToRoute('admin_userList');
    }
}

