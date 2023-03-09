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
    public function addLocation(
        LocationRepository $locationRepository,
        Request $request): Response
    {

        $location = new Location();

        $locationForm = $this->createForm(LocationType::class, $location);
        $locationForm->handleRequest($request);

//        dump($location);
        if ($locationForm->isSubmitted() && $locationForm->isValid()) {
           // dump($location);

            $location

//                ->setCity($locationForm->get('city')->getData());
//                ->setName($locationForm->get('name')->getData())
//                ->setStreet($locationForm->get('street')->getData())
                ->setLongitude(floatval($locationForm->get('longitude')->getData()))
                ->setLatitude(floatval($locationForm->get('latitude')->getData()));

            $locationRepository->save($location, true);
            $this->addFlash("success", "Lieu ajouté !");

            return $this->redirectToRoute('activity_home', ['id' => $location->getCity()->getId()]);
        }
   //     dump($location);

            return $this->render('location/location.html.twig', [
                'locationForm' => $locationForm->createView()
            ]);

        }
}
