<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use App\Utils\UpdateStatus;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('', name: 'activity_')]
class ActivityController extends AbstractController
{


    #[Route('/home', name: 'home')]
    #[Route('/', name: 'home2')]
    public function index(ActivityRepository $activityRepository, UpdateStatus $updateStatus): Response
    {

        //$updateStatus->updateStatusByCriteria($this);

        $activities = $activityRepository->findAll();

        return $this->render('main/index.html.twig', [
            'activities' => $activities
        ]);
    }



    #[Route('/activity/add', name: 'add')]
    #[Route('/activity/{id}', name: 'update')]
    public function add(int $id = 0, ActivityRepository $activityRepository,
                        Request            $request): Response
    {

        /**
         * @var User $user
         */
        $user = $this->getUser();
        $messageFlash = 'L\'activité a bien été modifiée !';
        dump($user);

        if ($id != 0) {

            $activity = $activityRepository->find($id);

            if ($user != $activity->getUser()) {
                return $this->redirectToRoute('activity_show', ['id' => $id]);
            }
        } else {
            $activity = new Activity();
        }

        $activityForm = $this->createForm(ActivityType::class, $activity);
        $activityForm->handleRequest($request);

        //TODO garder les informations déjà remplies dans le activity add si je clique sur ajouter un lieu
        if ($activityForm->isSubmitted() && $activityForm->isValid()) {
            dump($activity);
            if ($id == 0) {
                $activity->setUser($user);
                $activity->setCampus($user->getCampus());
                $activity->addUser($user);
                $messageFlash = 'L\'activité a bien été créée !';
            }

            $activityRepository->save($activity, true);
            $this->addFlash("success", $messageFlash);

            return $this->redirectToRoute('activity_home', ['id' => $activity->getId()]);
        }
        dump($activity);


        return $this->render('activity/activity.html.twig', [
            'activityForm' => $activityForm->createView(),
            'activity' => $activity
        ]);
    }

//function which allows user to see an activity's informations
    #[Route('/activity/show/{id}', name: 'show', requirements: ['id' => '\d+'])]
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

    #[Route('/activity/remove/{id}', name: 'remove')]
    public function removeActivity(int $id, ActivityRepository $activityRepository){

        $activity = $activityRepository->find($id);

        if($activity){

            $activityRepository->remove($activity, true);
            $this->addFlash("warning", "La sortie a été supprimée !");
        }else{
            throw $this->createNotFoundException("Cette sortie ne peut pas être supprimée !");
        }

        return $this->redirectToRoute('activity_home');
    }
}
