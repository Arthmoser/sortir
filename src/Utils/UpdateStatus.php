<?php

namespace App\Utils;

use App\Controller\ActivityController;
use App\Repository\ActivityRepository;
use App\Repository\StatusRepository;

class UpdateStatus
{

    public function updateStatusByCriteria(StatusRepository $statusRepository, ActivityRepository $activityRepository)
    {

        $statuses = $statusRepository->findAll();

        $activityRepository->findBy();

    }

}