<?php

namespace App\Controller\Api;

use App\Repository\LocationRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/location', name: 'api_location_')]
class LocationController extends AbstractController
{
    #[Route('/city/{id}', name: 'retrieve_by_cityId', methods: "GET")]
    public function retrieveByCityId(int $id, LocationRepository $locationRepository): Response
    {
        $locations = $locationRepository->findById($id, true);

        return $this->json($locations, 200, [], ['groups' => 'location_api']);
    }

    #[Route('/{id}', name: 'retrieve_by_Id', methods: "GET")]
    public function retrieveById(int $id, LocationRepository $locationRepository): Response
    {
        $locations = $locationRepository->findById($id, false);

        return $this->json($locations, 200, [], ['groups' => 'location_api']);
    }


}
