<?php

namespace App\Enum;

enum FlashEnum : string
{
    case SUCCESS = 'success';
    case DANGER = 'danger';
    case INFO = 'info';
    case WARNING = 'warning';

}
