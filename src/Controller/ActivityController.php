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
                        Request            $request): Response
    {


        /**
         * @var User $user
         */
        $user = $this->getUser();

        dump($user);

        $activity = new Activity();

        $activityForm = $this->createForm(ActivityType::class, $activity);
        $activityForm->handleRequest($request);

        if ($activityForm->isSubmitted() && $activityForm->isValid()) {

            $activity->setUser($user);
            $activity->setCampus($user->getCampus());

            $activityRepository->save($activity, true);
            $this->addFlash("success", "Activité créée ! ");

            return $this->redirectToRoute('main_home', ['id' => $activity->getId()]);
        }
        dump($activity);


        return $this->render('activity.html.twig', [
            'activityForm' => $activityForm->createView()
        ]);
    }

//function which allows user to see an activity's informations
    #[Route('/show/activity/{id}', name: 'showActivity', requirements: ['id' => '\d+'])]
    public function showActivity(
        int $id, ActivityRepository $activityRepository): Response
    {
        $activity = $activityRepository->find($id);

        if(!$activity){
            throw $this->createNotFoundException("Cette activité n'existe pas ! ");
        }

        return $this->render('activity/showActivity.html.twig', [
                'activity' => $activity
        ]);
    }
}