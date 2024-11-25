<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Lesson;
use App\Entity\LessonParticipant;
use App\Entity\User;
use App\Enum\RoleEnum;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Lesson>
 */
final class LessonVoter extends Voter
{


    public const VIEW = 'VIEW_LESSON';

    public function __construct(
        private readonly Security $security
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        return in_array($attribute, [self::VIEW])
            && $subject instanceof Lesson;
    }

    /**
     * @param string $attribute
     * @param Lesson $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::VIEW => $this->canUserViewLesson($user, $subject),
            default => false
        };
    }

    private function canUserViewLesson(User $user, Lesson $lesson): bool
    {
        return match (true) {
            $lesson->getLessonParticipants()->exists(
                fn(int $key, LessonParticipant $lessonParticipant): bool => $lessonParticipant->getOwner() === $user
            ) => true,
            $this->security->isGranted(RoleEnum::TEACHER->value, $user->getRoles()) => true,
            default => false
        };
    }
}
