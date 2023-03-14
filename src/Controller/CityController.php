<?php

namespace App\Controller;

use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/city/display', name: 'city_')]
class CityController extends AbstractController
{
    #[Route('/city/display', name: 'display')]
    public function displayCity(CityRepository $cityRepository, Request $request): Response
    {
        $cities = new City();

        $cities = $cityRepository->findAll();

        return $this->render('city/city.html.twig', [
            'cities' => $cities,
        ]);
    }


    #[Route('/city/remove/{id}', name : 'remove',  methods: ['GET'])]
    public function removeCity(int $id, CityRepository $cityRepository) : Response
    {
        $cities = $cityRepository->find($id);


        if($cities){
            $cityRepository->remove($cities, true );
            $this->addFlash("warning", "La ville a été supprimé ! ");
        } else {
            throw $this->createNotFoundException("Cet ville ne peut pas être supprimé ! ");
        }

        return $this->redirectToRoute('city_display');
    }
}
