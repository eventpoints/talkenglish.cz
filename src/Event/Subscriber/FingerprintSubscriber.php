<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\User;
use App\Exception\ShouldNotHappenException;
use App\Service\FingerPrintService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(event: RequestEvent::class, method: 'generateFingerprint', priority: 7)]
readonly class FingerprintSubscriber
{

    public function __construct(
        private Security           $security,
        private FingerPrintService $fingerPrintService,
    )
    {
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function generateFingerprint(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        if ($user instanceof User) {
            return;
        }

        $request = $event->getRequest();
        $session = $event->getRequest()->getSession();

        $fingerprint = $session->get('fingerprint');

        if (empty($fingerprint)) {
            $session->set('fingerprint', $this->fingerPrintService->generate(request: $request));
        }

    }
}
