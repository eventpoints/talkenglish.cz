<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\Quiz;
use App\Entity\User;
use App\Enum\Quiz\CategoryEnum;
use App\Enum\RoleEnum;
use App\Exception\ShouldNotHappenException;
use App\Repository\QuizRepository;
use App\Repository\UserRepository;
use App\Service\FingerPrintService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
