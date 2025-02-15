<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[AsEntityListener(event: Events::prePersist, method: 'slugifyTitle', entity: Quiz::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'slugifyTitle', entity: Quiz::class)]
readonly final class QuizSubscriber
{
    public function slugifyTitle(Quiz $quiz): void
    {
        $slugger = new AsciiSlugger();
        $title = strtolower($quiz->getTitle());
        $slug = $slugger->slug(string: $title, locale: 'en');
        $quiz->setSlug($slug->toString());
    }
}
