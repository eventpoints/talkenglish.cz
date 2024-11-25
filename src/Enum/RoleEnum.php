<?php

namespace App\Enum;

enum RoleEnum: string
{
    case USER = 'ROLE_USER';
    case STUDENT = 'ROLE_STUDENT';
    case TEACHER = 'ROLE_TEACHER';
    case ADMIN = 'ROLE_ADMIN';
    case OBSERVER = 'ROLE_OBSERVER';
}
