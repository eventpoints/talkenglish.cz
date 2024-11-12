<?php

namespace App\Entity;

use App\Enum\Quiz\CategoryEnum;
use App\Enum\Quiz\LevelEnum;
use App\Repository\QuizParticipationRepository;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
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
    private User $owner;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|CarbonImmutable $startAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|CarbonImmutable $endAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private null|CarbonImmutable $completedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    /**
     * @var Collection<int, Question>
     */
    #[ORM\ManyToMany(targetEntity: Question::class, inversedBy: 'quizParticipations')]
    private Collection $questions;

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'quizParticipation')]
    private Collection $answers;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], inversedBy: 'quizParticipation')]
    private ?Quiz $quiz = null;

    /**
     * @param User|null $owner
     */
    public function __construct(?User $owner, null|Quiz $quiz = null)
    {
        $this->owner = $owner;
        $this->createdAt = new CarbonImmutable();
        $this->startAt = new CarbonImmutable();
        $this->questions = new ArrayCollection();
        $this->answers = new ArrayCollection();
        $this->quiz = $quiz;
    }

    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
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

    public function isTimed(): ?bool
    {
        return $this->isTimed;
    }

    public function setTimed(bool $isTimed): static
    {
        $this->isTimed = $isTimed;

        return $this;
    }

    public function getCategoryEnum(): ?CategoryEnum
    {
        return $this->categoryEnum;
    }

    public function setCategoryEnum(?CategoryEnum $categoryEnum): void
    {
        $this->categoryEnum = $categoryEnum;
    }

    public function getLevelEnum(): ?LevelEnum
    {
        return $this->levelEnum;
    }

    public function setLevelEnum(?LevelEnum $levelEnum): void
    {
        $this->levelEnum = $levelEnum;
    }

    public function getIsTimed(): ?bool
    {
        return $this->isTimed;
    }

    public function setIsTimed(?bool $isTimed): void
    {
        $this->isTimed = $isTimed;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
        }

        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        $this->questions->removeElement($question);

        return $this;
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
            ? CarbonImmutable::now()->diffInSeconds($endAt)
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


    public function getCompletionTimeInMintues() : int
    {
        return $this->getStartAt()->diffInMinutes($this->getCompletedAt());
    }
}
