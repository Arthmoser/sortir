<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Location;
use App\Form\LocationType;
use App\Repository\ActivityRepository;
use App\Repository\CityRepository;
use App\Repository\LocationRepository;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/location', name: 'location_')]
class LocationController extends AbstractController
{
    #[Route('/add', name: 'add')]
    #[Route('/update/add/{id}', name: 'update_add')]
    public function addLocation(
        LocationRepository $locationRepository,
        Request            $request, int $id = 0): Response
    {

        $updatePath = '/location/update/add/' . $id;

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

            dump($request->getPathInfo());
            dump($updatePath);

            if ($request->getPathInfo() == $updatePath) {
                dump($id);
                return $this->redirectToRoute('activity_update', ['id' => $id]);
            } else {
                return $this->redirectToRoute('activity_add', ['id' => $location->getCity()->getId()]);
            }
        }
        //     dump($location);

        return $this->render('location/location.html.twig', [
            'locationForm' => $locationForm->createView()
        ]);

    }

    //function which allows user to see an activity's informations
    #[Route('/location/show/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function showLocation(int $id, LocationRepository $locationRepository): Response
    {
        $location = $locationRepository->find($id);

        if (!$location) {
            throw $this->createNotFoundException("Ce lieu n'existe pas ! ");
        }

        return $this->render('activity/showActivity.html.twig', [
            'location' => $location
        ]);
    }


}
