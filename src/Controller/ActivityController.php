<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Form\FilterType;
use App\Form\Model\FilterModel;
use App\Repository\ActivityRepository;
use App\Repository\StatusRepository;
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
    public function index(StatusRepository $statusRepository, ActivityRepository $activityRepository, UpdateStatus $updateStatus, Request $request): Response
    {
        $currentDate = new \DateTime();

        /**
         * @var User $user
         */
        $user = $this->getUser();
        $activities = $activityRepository->findNonArchivedActivity($currentDate);

        $updateStatus->updateStatusByCriteria($statusRepository, $activityRepository, $activities);


        $filterModel = new FilterModel();

        $filterForm = $this->createForm(FilterType::class, $filterModel);
        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted()) //TODO set form is valid
        {
            dump($filterModel);

           $activities = $activityRepository->filterActivities($filterModel, $user);

        }

        return $this->render('main/index.html.twig', [
            'activities' => $activities,
            'filterForm' => $filterForm->createView()

        ]);
    }


    #[Route('/activity/add', name: 'add')]
    #[Route('/activity/{id}', name: 'update')]
    public function add(ActivityRepository $activityRepository,
                        StatusRepository $statusRepository,
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

            if ($user !== $activity->getUser()) {
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

            $statuses = $statusRepository->findAll();

            if ($activityForm->get('save')->isClicked())
            {
                $activity->setStatus($statuses[0]);
            }
            elseif ($activityForm->get('publish')->isClicked())
            {
                $activity->setStatus($statuses[1]);
            }

            if ($request->getPathInfo() == $pathAdd)
            {
                $activity->setUser($user);
                $activity->setCampus($user->getCampus());
                $activity->addUser($user);
                $messageFlash = 'L\'activité a bien été créée !';
            }

            $activityRepository->save($activity, true);
            $this->addFlash("success", $messageFlash);

            return $this->redirectToRoute('activity_home', ['id' => $activity->getId()]);
        }


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
        $currentDate = new \DateTime;
        $messageType = 'error';
        $flashMessage = 'Vous êtes bien inscrit !';

        /**
         * @var User $user
         */
        $user = $this->getUser();

        $activity = $activityRepository->find($id);

        if($activity)
        {
            //when the user tries to register for the activity
            if ($request->getPathInfo() == $pathRegister) {
                if ($activity->getUsers()->contains($user)) {
                    $flashMessage = 'Vous êtes déjà inscrit !';
                } elseif($activity->getStatus()->getType() != $status) {
                    $flashMessage = 'Cette activité n\'est pas ouverte à l\'inscription !';
                } elseif($activity->getRegistrationDeadLine() < $currentDate) {
                    $flashMessage =  'Cette activité n\'est plus ouverte à l\'inscription !';
                } elseif(count($activity->getUsers()) >= $activity->getMaxRegistrationNb()) {
                    $flashMessage = 'Cette activité est complète !';
                } else {
                    $activity->addUser($user);
                    $messageType = 'success';
                    $activityRepository->save($activity, true);
                }

                //when the user tries to unregister for the activity
            } elseif ($request->getPathInfo() == $pathUnRegister) {
                if ($activity->getUsers()->contains($user)) {
                    $activity->removeUser($user);
                    $messageType = 'success';
                    $flashMessage = 'Vous êtes bien désinscrit !';
                    $activityRepository->save($activity, true);
                } else {
                    $flashMessage = 'Vous n\'êtes pas inscrit à l\'activité !';
                }
            }

        } else {
            $flashMessage = 'L\'activité n\'éxiste pas !';
        }

        $this->addFlash($messageType, $flashMessage);
        return $this->redirectToRoute('activity_home');
    }

    #[Route('/canceled/{id}', name: 'canceled')]
    public function canceled($id, ActivityRepository $activityRepository, StatusRepository $statusRepository) {

        $statusCodeCanceled = 'ANN';
        $messageType = 'error';
        $flashMessage = '';

        $activity = $activityRepository->find($id);

       if ($activity)
       {
            $statuses = $statusRepository->findAll();
           foreach ($statuses as $status)
           {
               if ($status->getStatusCode() == $statusCodeCanceled)
               {
                   $activity->setStatus($status);
                   $activityRepository->save($activity, true);
                   $flashMessage = 'L\'activité est bien annulé';
                   $messageType = 'success';
                   break;
               }
           }
       } else
       {
           $flashMessage = 'Une erreur est survenue pendant le processus d\'annulation';
       }

        $this->addFlash($messageType, $flashMessage);
        return $this->redirectToRoute('activity_home');

    }

}
