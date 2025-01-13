<?php

declare(strict_types=1);

namespace App\Enum\Job;

enum EmploymentTypeEnum : string
{
    case FULL_TIME = 'employment.full_time';
    case PART_TIME = 'employment.part_time';
    case CONTRACT = 'employment.contract';
    case INTERNSHIP = 'employment.internship';

}
