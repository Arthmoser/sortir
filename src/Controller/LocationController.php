<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Location;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/location', name: 'main_')]
class LocationController extends AbstractController
{
    #[Route('/add', name: 'addLocation')]
    public function addLocation(LocationRepository $locationRepository, Request $request): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $location = new Location();

        $locationForm = $this->createForm(LocationType::class,$location);
        $locationForm->handleRequest($request);

        if ($locationForm->isSubmitted() && $locationForm->isValid()) {
            $location
                ->setCity("city")
                ->setName("name")
                ->setStreet("street")
                ->setLongitude("longitude")
                ->setLatitude("latitude");

            $locationRepository->save($location, true);
            $this->addFlash("success", "Lieu ajouté !");

            return $this->redirectToRoute('main_home', ['id' => $location->getId()]);

        }
            return $this->render('location.html.twig', [
                'locationForm' => $locationForm->createView()
            ]);

        }
}
