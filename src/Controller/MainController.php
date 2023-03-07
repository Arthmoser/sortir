<?php

namespace App\Controller;


use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/home', name: 'main_home')]
    #[Route('/', name: 'main_home2')]
    public function index(ActivityRepository $activityRepository): Response
    {

        $activities = $activityRepository->findAll();

        return $this->render('main/index.html.twig', [
            'activities' => $activities
        ]);
    }

}
