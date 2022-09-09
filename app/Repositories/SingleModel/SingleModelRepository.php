<?php

namespace App\Repositories\SingleModel;
use App\Repositories\SingleModel\iSingleModelRepository;
use App\Models\EmployeeTypes;
use App\Models\PostType;
use App\Models\Reflection;
use App\Models\PostPrivacy;

class SingleModelRepository implements iSingleModelRepository{

    public $employeeTypes;
    public $postType;
    public $postPrivacy;
    public $reflections;

    public function __construct(EmployeeTypes $employeeTypes, PostType $postType, PostPrivacy $postPrivacy, Reflection $reflections)
    {
        $this->employeeTypes = $employeeTypes;
        $this->postType = $postType;
        $this->postPrivacy = $postPrivacy;
        $this->reflections = $reflections;
    }

}