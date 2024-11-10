<?php

namespace App\Security\Voter;

use App\Entity\Lesson;
use App\Entity\LessonParticipant;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class LessonVoter extends Voter
{
    public const VIEW = 'VIEW_LESSON';

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
             self::VIEW => $this->canUserJoinLesson($user, $subject),
             default => false
        };
    }

    private function canUserJoinLesson(User $user, Lesson $lesson): bool
    {
        return $lesson->getLessonParticipants()->exists(
            fn(int $key, LessonParticipant $lessonParticipant) => $lessonParticipant->getOwner() === $user
        );
    }
}
