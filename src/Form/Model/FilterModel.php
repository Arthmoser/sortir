<?php

namespace App\Form\Model;

class FilterModel
{

    private $campus;

    private $search;

    private $startingDateTime;
    private $endingDateTime;

    private $isOrganiser;

    private $isRegistered;

    private $isNotRegistered;

    private $availableActivity;



    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param mixed $campus
     */
    public function setCampus($campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return mixed
     */
    public function getStartingDateTime()
    {
        return $this->startingDateTime;
    }

    /**
     * @param mixed $startingDateTime
     */
    public function setStartingDateTime($startingDateTime): void
    {
        $this->startingDateTime = $startingDateTime;
    }

    /**
     * @return mixed
     */
    public function getEndingDateTime()
    {
        return $this->endingDateTime;
    }

    /**
     * @param mixed $endingDateTime
     */
    public function setEndingDateTime($endingDateTime): void
    {
        $this->endingDateTime = $endingDateTime;
    }

    /**
     * @return mixed
     */
    public function getIsOrganiser()
    {
        return $this->isOrganiser;
    }

    /**
     * @param mixed $isOrganiser
     */
    public function setIsOrganiser($isOrganiser): void
    {
        $this->isOrganiser = $isOrganiser;
    }

    /**
     * @return mixed
     */
    public function getIsRegistered()
    {
        return $this->isRegistered;
    }

    /**
     * @param mixed $isRegistered
     */
    public function setIsRegistered($isRegistered): void
    {
        $this->isRegistered = $isRegistered;
    }

    /**
     * @return mixed
     */
    public function getIsNotRegistered()
    {
        return $this->isNotRegistered;
    }

    /**
     * @param mixed $isNotRegistered
     */
    public function setIsNotRegistered($isNotRegistered): void
    {
        $this->isNotRegistered = $isNotRegistered;
    }

    /**
     * @return mixed
     */
    public function getAvailableActivity()
    {
        return $this->availableActivity;
    }

    /**
     * @param mixed $availableActivity
     */
    public function setAvailableActivity($availableActivity): void
    {
        $this->availableActivity = $availableActivity;
    }


    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param mixed $search
     */
    public function setSearch($search): void
    {
        $this->search = $search;
    }




}