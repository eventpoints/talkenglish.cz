<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use App\Repository\UserRepository;

class FingerprintAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UserRepository $userRepository
    )
    {
    }

    /**
     * Only supports fingerprint if no email is submitted in the request
     */
    public function supports(Request $request): bool
    {
        if ($request->request->get('email')) {
            return false;
        }

        return $request->getSession()->has('fingerprint');
    }

    public function authenticate(Request $request): Passport
    {
        $fingerprint = $request->getSession()->get('fingerprint');

        if (!$fingerprint) {
            throw new AuthenticationException('No fingerprint provided.');
        }

        $user = $this->userRepository->findOneBy(['fingerprint' => $fingerprint]);

        if (!$user) {
            throw new UserNotFoundException('User not found for provided fingerprint.');
        }

        return new SelfValidatingPassport(new UserBadge($user->getFingerprint()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }
}
