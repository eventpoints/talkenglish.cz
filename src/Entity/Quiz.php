<?php

namespace App\Entity;

use App\Enum\Quiz\CategoryEnum;
use App\Enum\Quiz\LevelEnum;
use App\Repository\QuizRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private null|Uuid $id = null;

    /**
     * @var Collection<int, Question>
     */
    #[ORM\ManyToMany(targetEntity: Question::class, inversedBy: 'quizzes')]
    private Collection $questions;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(enumType: CategoryEnum::class)]
    private null|CategoryEnum $categoryEnum;

    #[ORM\Column(enumType: LevelEnum::class)]
    private null|LevelEnum $levelEnum;

    #[ORM\Column(type: Types::TEXT, length: 500)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private null|int $timeLimitInMinutes = null;

    /**
     * @var Collection<int, QuizParticipation>
     */
    #[ORM\OneToMany(targetEntity: QuizParticipation::class, mappedBy: 'quiz')]
    private Collection $quizParticipations;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->quizParticipations = new ArrayCollection();
        $this->createdAt = new CarbonImmutable();
    }

    public function getId(): null|Uuid
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?CarbonImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
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

    public function getTimeLimitInMinutes(): ?int
    {
        return $this->timeLimitInMinutes;
    }

    public function setTimeLimitInMinutes(?int $timeLimitInMinutes): void
    {
        $this->timeLimitInMinutes = $timeLimitInMinutes;
    }


    /**
     * @return Collection<int, QuizParticipation>
     */
    public function getQuizParticipations(): Collection
    {
        return $this->quizParticipations;
    }

    public function addQuizParticipation(QuizParticipation $quizParticipation): static
    {
        if (!$this->quizParticipations->contains($quizParticipation)) {
            $this->quizParticipations->add($quizParticipation);
        }

        return $this;
    }

    public function removeQuizParticipation(QuizParticipation $quizParticipation): static
    {
        $this->quizParticipations->removeElement($quizParticipation);
        return $this;
    }
}