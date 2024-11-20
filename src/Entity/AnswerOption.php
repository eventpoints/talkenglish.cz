<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AnswerOptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AnswerOptionRepository::class)]
class AnswerOption
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private Uuid|null $id = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column]
    private ?bool $isCorrect = false;

    #[ORM\ManyToOne(inversedBy: 'answerOptions', cascade: ['remove'])]
    private ?Question $question = null;

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\ManyToMany(targetEntity: Answer::class, mappedBy: 'answerOption',cascade: ['remove'])]
    private Collection $answers;

    /**
     * @param string|null $content
     * @param bool|null $isCorrect
     */
    public function __construct(?string $content = null, ?bool $isCorrect = null, ?Question $question = null)
    {
        $this->content = $content;
        $this->isCorrect = $isCorrect;
        $this->question = $question;
        $this->answers = new ArrayCollection();
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

    public function getIsCorrect(): ?bool
    {
        return $this->isCorrect;
    }

    public function setIsCorrect(bool $isCorrect): static
    {
        $this->isCorrect = $isCorrect;

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

    public function __toString(): string
    {
        $isCorrect = $this->getIsCorrect() ? " (correct)" : " (wrong)";

        return $this->getContent() . $isCorrect;
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
            $answer->addAnswerOption($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): static
    {
        if ($this->answers->removeElement($answer)) {
            $answer->removeAnswerOption($this);
        }

        return $this;
    }

}
