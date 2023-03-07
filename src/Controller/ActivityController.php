<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActivityController extends AbstractController
{
    #[Route('/activity', name: 'main_')]
    public function activity(): Response
    {
        return $this->render('activity.html.twig', [
            'controller_name' => 'ActivityController',
        ]);
    }

    #[Route('/activity/add', name: 'add')]
    public function add(ActivityRepository $activityRepository,
                        Request $request): Response
    {

        $activities = new Activity();

        $activityForm = $this->createForm(ActivityType::class, $activities);
        $activityForm->handleRequest($request);

        if($activityForm->isSubmitted() && $activityForm->isValid()){

            $activityRepository -> save($activities, true);
            $this->addFlash("success", "Activité créer ! ");
        }
        dump($activities);
        return $this->render('activity.html.twig', [
            'activityForm' => $activityForm->createView()
        ]);
    }



}
