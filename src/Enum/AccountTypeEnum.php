<?php

declare(strict_types=1);

namespace App\Enum;

enum AccountTypeEnum : string
{
    case TEACHER = 'teacher';
    case STUDENT = 'student';
}
