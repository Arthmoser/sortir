<?php

namespace App\Utils;

use App\Controller\ActivityController;
use App\Entity\Status;
use App\Repository\ActivityRepository;
use App\Repository\StatusRepository;

class UpdateStatus
{

    public function updateStatusByCriteria(StatusRepository $statusRepository, ActivityRepository $activityRepository, $activities)
    {
        $statusClosed = new Status();
        $statusInProgress = new Status();
        $statusPast = new Status();
        $statusArchived = new Status();
        $statusCreated = new Status();

        $currentDateTime = new \DateTime;
        $oneMonthBeforeCurrentDateTime = clone $currentDateTime;
        $oneMonthBeforeCurrentDateTime->modify('-1 month');

        $statuses = $statusRepository->findAll();

        foreach ($statuses as $status)
        {
            if ($status->getStatusCode() == 'CLO')
            {
                $statusClosed = $status;
            }
            elseif($status->getStatusCode() == 'AEC')
            {
                $statusInProgress = $status;
            }
            elseif($status->getStatusCode() == 'PAS')
            {
                $statusPast = $status;
            }
            elseif($status->getStatusCode() == 'HIS')
            {
                $statusArchived = $status;
            }
            elseif($status->getStatusCode() == 'CRE')
            {
                $statusCreated = $status;
            }
        }


        foreach ($activities as $key => $activity)
        {
            if ($activity->getStatus() == $statusCreated && $activity->getStartingDateTime() < $currentDateTime)
            {
                unset($activities[$key]);
                $activityRepository->remove($activity, true);
            }
            else
            {
                if ($activity->getStartingDateTime() < $oneMonthBeforeCurrentDateTime) {
                    $activity->setStatus($statusArchived);
                } elseif ($activity->getStartingDateTime() > $currentDateTime
                    && $activity->getRegistrationDeadLine() < $currentDateTime) {
                    $activity->setStatus($statusClosed);
                } elseif ($activity->getStartingDateTime() <= $currentDateTime) {
                    $endingDateTime = clone $activity->getStartingDateTime();
                    $endingDateTime->modify('+ ' . $activity->getDuration() . ' minute');

                    if ($endingDateTime > $currentDateTime) {
                        $activity->setStatus($statusInProgress);
                    } else {
                        $activity->setStatus($statusPast);
                    }
                }
                $activities[$key] = $activity;
                $activityRepository->save($activity, true);
            }
        }
    }


}