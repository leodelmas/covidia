<?php

namespace App\Entity;

class PlanningSearch {

    private $taskCategories;

    private $workTimeType;

    /**
     * PlanningSearch constructor.
     */
    public function __construct() {}

    /**
     * @return mixed
     */
    public function getTaskCategories() {
        return $this->taskCategories;
    }

    /**
     * @param mixed $taskCategories
     * @return PlanningSearch
     */
    public function setTaskCategories($taskCategories): PlanningSearch {
        $this->taskCategories = $taskCategories;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWorkTimeType() {
        return $this->workTimeType;
    }

    /**
     * @param mixed $workTimeType
     * @return PlanningSearch
     */
    public function setWorkTimeType($workTimeType): PlanningSearch {
        $this->workTimeType = $workTimeType;
        return $this;
    }
}