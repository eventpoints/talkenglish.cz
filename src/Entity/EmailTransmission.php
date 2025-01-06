<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmailTransmissionRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EmailTransmissionRepository::class)]
class EmailTransmission
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private null|Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'emailTransmissions')]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'emailTransmissions')]
    private ?Quiz $quiz = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    /**
     * @param User|null $owner
     * @param Quiz|null $quiz
     */
    public function __construct(null|User $owner, null|Quiz $quiz)
    {
        $this->owner = $owner;
        $this->quiz = $quiz;
        $this->createdAt = new CarbonImmutable();
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

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CarbonImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

}
