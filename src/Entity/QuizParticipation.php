<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\QuizParticipationRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: QuizParticipationRepository::class)]
class QuizParticipation
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private null|Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'quizParticipations')]
    private null|User $owner;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|CarbonImmutable $startAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|CarbonImmutable $endAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private null|CarbonImmutable $completedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'quizParticipation', orphanRemoval: true)]
    private Collection $answers;

    #[ORM\ManyToOne(cascade: [], inversedBy: 'quizParticipation')]
    private null|Quiz $quiz = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private null|string $score = null;

    /**
     * @param User|null $owner
     * @param Quiz|null $quiz
     */
    public function __construct(null|User $owner = null, null|Quiz $quiz = null)
    {
        $this->owner = $owner;
        $this->createdAt = new CarbonImmutable();
        $this->startAt = new CarbonImmutable();
        $this->answers = new ArrayCollection();
        $this->quiz = $quiz;
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getOwner(): null|User
    {
        return $this->owner;
    }

    public function setOwner(null|User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getStartAt(): ?CarbonImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(?CarbonImmutable $startAt): void
    {
        $this->startAt = $startAt;
    }

    public function getCompletedAt(): ?CarbonImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?CarbonImmutable $completedAt): void
    {
        $this->completedAt = $completedAt;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CarbonImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): static
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setQuizParticipation($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): static
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuizParticipation() === $this) {
                $answer->setQuizParticipation(null);
            }
        }

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }


    public function getQuizDurationInSeconds(): int
    {
        $endAt = $this->startAt->addMinutes($this->quiz->getTimeLimitInMinutes());

        return CarbonImmutable::now()->diffInSeconds($endAt, false) > 0
            ? (int)CarbonImmutable::now()->diffInSeconds($endAt)
            : 0;
    }

    public function getEndAt(): ?CarbonImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?CarbonImmutable $endAt): void
    {
        $this->endAt = $endAt;
    }

    public function getCompletionTimeInMintues(): float
    {
        return round($this->getStartAt()->diffInMinutes($this->getCompletedAt()), 2);
    }

    public function getCalculatedQuizEndAt(): CarbonImmutable
    {
        return $this->getStartAt()->addMinutes($this->getQuiz()->getTimeLimitInMinutes());
    }

    public function getScore(): null|string
    {
        return $this->score;
    }

    public function setScore(null|string $score): static
    {
        $this->score = $score;

        return $this;
    }

}
