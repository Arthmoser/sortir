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
    public function add(ActivityRepository $activityRepository,
                        Request $request, int $id = 0): Response
    {
        $pathAdd = '/activity/add';
        $pathUpdate = '/activity/' . $id;
        $messageFlash = 'L\'activité a bien été modifiée !';

        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($id != 0) {

            $activity = $activityRepository->find($id);

            if ($user != $activity->getUser()) {
                return $this->redirectToRoute('activity_show', ['id' => $id]);
            }

        } else {
            $activity = new Activity();
        }

        if ($request->getPathInfo() == $pathUpdate && $user != $activity->getUser()) {

                return $this->redirectToRoute('activity_show', ['id' => $id]);

        }

        $activityForm = $this->createForm(ActivityType::class, $activity);
        $activityForm->handleRequest($request);

        //TODO garder les informations déjà remplies dans le activity add si je clique sur ajouter un lieu
        if ($activityForm->isSubmitted() && $activityForm->isValid()) {
            dump($activity);
            if ($request->getPathInfo() == $pathAdd) {
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

    #[Route('/activity/remove/{id}', name: 'remove', requirements: ['id' => '\d+'])]
    public function removeActivity(int $id, ActivityRepository $activityRepository)
    {

        $activity = $activityRepository->find($id);

        if($activity){

            $activityRepository->remove($activity, true);
            $this->addFlash("warning", "La sortie a bien été supprimée !");
        }else{
            throw $this->createNotFoundException("Cette sortie ne peut pas être supprimée !");
        }

        return $this->redirectToRoute('activity_home');
    }

    #[Route('/register/{id}', name: 'register')]
    #[Route('/unregister/{id}', name: 'unregister')]
    public function registerActivity(int $id, ActivityRepository $activityRepository, Request $request)
    {
        $pathRegister = '/register/' . $id;
        $pathUnRegister = '/unregister/' . $id;
        $status = 'Ouverte';

        /**
         * @var User $user
         */
        $user = $this->getUser();

        $activity = $activityRepository->find($id);

        if($activity && $activity->getStatus()->getType() == $status) {

            //when the user tries to register for the activity
            if ($request->getPathInfo() == $pathRegister) {
                if ($activity->getUsers()->contains($user)) {
                    $this->addFlash("error", 'Vous êtes déjà inscrit !');
                    return $this->redirectToRoute('activity_home');
                } else {
                    $activity->addUser($user);
                    $this->addFlash("success", "Vous êtes bien inscrit !");
                }

                //when the user tries to unregister for the activity
            } elseif ($request->getPathInfo() == $pathUnRegister) {
                if ($activity->getUsers()->contains($user)) {
                    $activity->removeUser($user);
                } else {
                    $this->addFlash("error", 'Vous n\'êtes pas inscrit à l\'activité !');
                    return $this->redirectToRoute('activity_home');
                }
            }

            $activityRepository->save($activity, true);

        }

        return $this->redirectToRoute('activity_home');
    }

}
