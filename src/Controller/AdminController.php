<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\User;
use App\Form\CampusType;
use App\Form\CityType;
use App\Form\RegistrationFormType;
use App\Repository\CampusRepository;
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
    #[Route('/campus/{id}', name: 'campus_update')]
    public function list(CampusRepository $campusRepository, Request $request, int $id = 0): Response
    {
        $campus = new Campus();
        $campus2 = new Campus();
        $isUpdate = false;
        $campuses = $campusRepository->findBy([], ['name' => 'ASC']);

        foreach ($campuses as $camp)
        {
            if ($camp->getId() == $id)
            {
                $campus2 = $camp;
            }
        }

        $campusForm = $this->createForm(CampusType::class, $campus);
        $campusForm2 = $this->createForm(CampusType::class, $campus2);
        $campusForm->handleRequest($request);
        $campusForm2->handleRequest($request);


        if ($request->getPathInfo() == '/admin/campus/' . $id && !$campusForm2->isSubmitted()) {
            $isUpdate = true;
//            $this->entityManager = $entityManager;
            return $this->render('admin/campus/list.html.twig', [
                'campuses' => $campuses,
                'campusForm' => $campusForm->createView(),
                'campusForm2' => $campusForm2->createView(),
                'isUpdate' => $isUpdate,
                'id' => $id
            ]);
        }


        if ($campusForm2->isSubmitted()) {//TODO isValid condition
            dump($campus2);
            $campusRepository->save($campus2, true);
            $this->addFlash("success", "campus modifié ! ");
            $campus2 = $campusForm->getData();
            return $this->redirectToRoute('admin_campus_list', ['id' => $id]);
        }

        if ($campusForm->isSubmitted()) {//TODO isValid condition
            dump($campus);
            $campusRepository->save($campus, true);
            $this->addFlash("success", "campus ajouter ! ");
            $campus = $campusForm->getData();
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
            $this->addFlash("warning", "Le campus a été supprimé ! ");
        } else {
            throw $this->createNotFoundException("Ce campus ne peut pas être supprimé !");
        }
        return $this->redirectToRoute('admin_campus_list');

    }

    #[Route('/display/{id}', name: 'city_display')]
    public function displayCity(CityRepository $cityRepository, Request $request, int $id = 0): Response
    {
        $city = new City();

        $cities = $cityRepository->findAll();
        $cityForm = $this->createForm(CityType::class, $city);
        $cityForm->handleRequest($request);
        if($cityForm->isSubmitted() && $cityForm->isValid()){
            dump($city);

            $cityRepository->save($city, true );
            $this->addFlash('success', 'Ville ajoutée ! ');

            return $this->redirectToRoute('city_display', ['id' => $id]);
        }
        return $this->render('city/city.html.twig', [
            'cityForm' => $cityForm->createView(),
            'cities' => $cities
        ]);
    }


    #[Route('/remove/{id}', name : 'city_remove')]
    public function removeCity(int $id, CityRepository $cityRepository, EntityManagerInterface $entityManager) : Response
    {
        $citie = $cityRepository->find($id);
        $citie = $entityManager->getRepository(City::class)->find($id);
        try {
            $entityManager->remove($citie);
            $entityManager->flush();
            $this->addFlash("warning", "La ville a été supprimé ! ");
        }catch (\Exception $e) {
            $this->addFlash("error", 'Cette ville ne peut pas être supprimé ! ');
        }
        return $this->redirectToRoute('city_display');
    }

}
