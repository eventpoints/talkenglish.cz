<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AnswerRepository::class)]
class Answer
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid|null $id = null;

    /**
     * @var Collection<int, AnswerOption>
     */
    #[ORM\ManyToMany(targetEntity: AnswerOption::class, inversedBy: 'answers',cascade: ['remove'])]
    private Collection $answers;

    #[ORM\ManyToOne(inversedBy: 'answers')]
    private ?Question $question = null;

    #[ORM\ManyToOne(inversedBy: 'answers')]
    private ?QuizParticipation $quizParticipation = null;

    /**
     * @param QuizParticipation $quizParticipation
     * @param Question $question
     */
    public function __construct(
        QuizParticipation $quizParticipation,
        Question $question
    )
    {
        $this->quizParticipation = $quizParticipation;
        $this->question = $question;
        $this->answers = new ArrayCollection();
    }


    public function getId(): null|Uuid
    {
        return $this->id;
    }

    /**
     * @return Collection<int, AnswerOption>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswerOption(AnswerOption $answerOption): static
    {
        if (!$this->answers->contains($answerOption)) {
            $this->answers->add($answerOption);
        }

        return $this;
    }

    public function removeAnswerOption(AnswerOption $answerOption): static
    {
        $this->answers->removeElement($answerOption);

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getQuizParticipation(): ?QuizParticipation
    {
        return $this->quizParticipation;
    }

    public function setQuizParticipation(?QuizParticipation $quizParticipation): static
    {
        $this->quizParticipation = $quizParticipation;

        return $this;
    }
    
}
