<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/activity', name: 'main_')]
class ActivityController extends AbstractController
{

    #[Route('/add', name: 'add')]
    public function add(ActivityRepository $activityRepository,
                        Request $request): Response
    {


        /**
         * @var User $user
         */
        $user = $this->getUser();

        dump($user);

        $activity = new Activity();

        $activityForm = $this->createForm(ActivityType::class, $activity);
        $activityForm->handleRequest($request);

        if($activityForm->isSubmitted() && $activityForm->isValid()){

            $activity->setUser($user);
            $activity->setCampus($user->getCampus());

            $activityRepository -> save($activity, true);
            $this->addFlash("success", "Activité créer ! ");

            return $this->redirectToRoute('main_home', ['id' => $activity->getId()]);
        }
        dump($activity);



        return $this->render('activity.html.twig', [
            'activityForm' => $activityForm->createView()
        ]);
    }



}
