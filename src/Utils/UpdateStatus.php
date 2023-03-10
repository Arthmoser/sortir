<?php

namespace App\Utils;

use App\Controller\ActivityController;
use App\Repository\ActivityRepository;
use App\Repository\StatusRepository;

class UpdateStatus
{

    public function updateStatusByCriteria(StatusRepository $statusRepository, ActivityRepository $activityRepository)
    {
        $currentDate = new \DateTime;

        $statuses = $statusRepository->findAll();

        $status = '';

        $activities = $activityRepository->findBySomeField($currentDate, $status);


        foreach ($activities as $activity)
        {
            $activity->setStatus($statuses[2]);
        }

        return $activities;

    }

}