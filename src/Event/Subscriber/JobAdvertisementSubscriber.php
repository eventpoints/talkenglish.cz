<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\User;
use App\Enum\FlashEnum;
use App\Exception\ShouldNotHappenException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener(event: RequestEvent::class, method: 'requireFullAccountToAdvertiseJob', priority: 7)]
readonly class JobAdvertisementSubscriber
{

    public function __construct(
        private Security              $security,
        private UrlGeneratorInterface $urlGenerator,
    )
    {
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function requireFullAccountToAdvertiseJob(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$event->isMainRequest()) {
            return;
        }

        $route = $event->getRequest()->get('_route');
        if ($route !== 'create_job') {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        if (!empty($user->getEmail()) || !empty($user->getPassword())) {
            return;
        }

        // @phpstan-ignore-next-line
        $request->getSession()->getFlashBag()->add(FlashEnum::WARNING->value, 'Please complete your account to advertise a job.');
        $redirectUrl = $this->urlGenerator->generate('complete_registration');
        $event->setResponse(new RedirectResponse(url: $redirectUrl));
    }
}
