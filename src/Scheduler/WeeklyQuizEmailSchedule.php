<?php

declare(strict_types=1);

namespace App\Scheduler;

use App\Message\Message\WeeklyQuizEmailNotification;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule]
class WeeklyQuizEmailSchedule implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())->add(
            RecurringMessage::every('1 minute', new WeeklyQuizEmailNotification()),
        );
    }
}
