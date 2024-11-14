<?php

namespace App\Event\Subscriber;

use App\Entity\Lesson;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


readonly class LessonSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private Security $security
    )
    {
    }

    /**
     * @return array<string, array<string>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['prePersist'],
        ];
    }


    public function prePersist(BeforeEntityPersistedEvent $args): void
    {
        $entity = $args->getEntityInstance();
        if (!$entity instanceof Lesson) {
            return;
        }

        $currentUser = $this->security->getUser();
        if(!$currentUser instanceof User){
            return;
        }

        $entity->setOwner($currentUser);
    }

}