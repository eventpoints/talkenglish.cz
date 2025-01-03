<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Quiz\QuestionTypeEnum;
use App\Enum\Quiz\CategoryEnum;
use App\Repository\QuestionRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private null|Uuid $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(enumType: QuestionTypeEnum::class)]
    private null|QuestionTypeEnum $questionTypeEnum = QuestionTypeEnum::MULTIPLE_CHOICE;

    #[ORM\Column(enumType: CategoryEnum::class)]
    private null|CategoryEnum $categoryEnum = CategoryEnum::GENERAL;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    /**
     * @var Collection<int, AnswerOption>
     */
    #[ORM\OneToMany(targetEntity: AnswerOption::class, mappedBy: 'question',cascade: ['persist'])]
    private Collection $answerOptions;

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'question', cascade: ['remove'])]
    private Collection $answers;

    /**
     * @var Collection<int, Quiz>
     */
    #[ORM\ManyToMany(targetEntity: Quiz::class, mappedBy: 'questions')]
    private Collection $quizzes;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    private null|QuestionExtra $questionExtra = null;

    /**
     * @param string|null $content
     * @param QuestionTypeEnum|null $questionTypeEnum
     * @param CategoryEnum|null $categoryEnum
     */
    public function __construct(
        null|string           $content = null,
        null|QuestionTypeEnum $questionTypeEnum = QuestionTypeEnum::FILL_IN_THE_BLACK,
        null|CategoryEnum     $categoryEnum = CategoryEnum::GENERAL,
    )
    {
        $this->content = $content;
        $this->questionTypeEnum = $questionTypeEnum;
        $this->categoryEnum = $categoryEnum;
        $this->createdAt = CarbonImmutable::now();
        $this->answerOptions = new ArrayCollection();
        $this->answers = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
    }


    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getQuestionTypeEnum(): ?QuestionTypeEnum
    {
        return $this->questionTypeEnum;
    }

    public function setQuestionTypeEnum(?QuestionTypeEnum $questionTypeEnum): void
    {
        $this->questionTypeEnum = $questionTypeEnum;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CarbonImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function __toString(): string
    {
        return $this->content;
    }

    public function getCategoryEnum(): ?CategoryEnum
    {
        return $this->categoryEnum;
    }

    public function setCategoryEnum(?CategoryEnum $categoryEnum): void
    {
        $this->categoryEnum = $categoryEnum;
    }
    /**
     * @return Collection<int, AnswerOption>
     */
    public function getAnswerOptions(): Collection
    {
        return $this->answerOptions;
    }

    public function addAnswerOption(AnswerOption $answerOption): static
    {
        if (!$this->answerOptions->contains($answerOption)) {
            $this->answerOptions->add($answerOption);
            $answerOption->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswerOption(AnswerOption $answerOption): static
    {
        if ($this->answerOptions->removeElement($answerOption)) {
            // set the owning side to null (unless already changed)
            if ($answerOption->getQuestion() === $this) {
                $answerOption->setQuestion(null);
            }
        }

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
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): static
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): static
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->addQuestion($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): static
    {
        if ($this->quizzes->removeElement($quiz)) {
            $quiz->removeQuestion($this);
        }

        return $this;
    }

    public function getQuestionExtra(): ?QuestionExtra
    {
        return $this->questionExtra;
    }

    public function setQuestionExtra(?QuestionExtra $questionExtra): static
    {
        $this->questionExtra = $questionExtra;

        return $this;
    }

}
