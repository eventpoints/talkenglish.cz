<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LessonRepository;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Order;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
class Lesson
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private null|Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(length: 255)]
    private ?string $currency = 'CZK';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private null|CarbonInterface $startAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private null|CarbonInterface $endAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $onlineUrl = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    /**
     * @var Collection<int, LessonParticipant>
     */
    #[ORM\OneToMany(targetEntity: LessonParticipant::class, mappedBy: 'lesson',cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['createdAt' => Order::Descending->value])]
    private Collection $lessonParticipants;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'lesson')]
    private Collection $comments;

    #[ORM\ManyToOne]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'lessons')]
    private ?User $teacher = null;

    public function __construct()
    {
        $this->createdAt = new CarbonImmutable();
        $this->lessonParticipants = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }


    public function getId(): null|Uuid
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOnlineUrl(): ?string
    {
        return $this->onlineUrl;
    }

    public function setOnlineUrl(?string $onlineUrl): static
    {
        $this->onlineUrl = $onlineUrl;

        return $this;
    }

    public function getStartAt(): CarbonInterface|null
    {
        return $this->startAt;
    }

    public function setStartAt(CarbonInterface|DateTimeImmutable|null $startAt): void
    {
        if ($startAt instanceof DateTimeImmutable) {
            $this->startAt = CarbonImmutable::parse($startAt);
            return;
        }
        $this->startAt = $startAt;
    }

    public function getEndAt(): CarbonInterface|null
    {
        return $this->endAt;
    }

    public function setEndAt(CarbonInterface|DateTimeImmutable|null $endAt): void
    {
        if ($endAt instanceof DateTimeImmutable) {
            $this->endAt = CarbonImmutable::parse($endAt);
            return;
        }

        $this->endAt = $endAt;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CarbonImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getDuration(): string
    {
        return $this->getStartAt()->diffForHumans(short: true);
    }

    public function getIsStartingIn(int $minutes): bool
    {
        $now = CarbonImmutable::now();
        $startAt = $this->getStartAt();

        return $startAt->greaterThan($now) && $startAt->lessThanOrEqualTo($now->addMinutes($minutes));
    }

    /**
     * @return Collection<int, LessonParticipant>
     */
    public function getLessonParticipants(): Collection
    {
        return $this->lessonParticipants;
    }

    public function addLessonParticipant(LessonParticipant $lessonStudent): static
    {
        if (!$this->lessonParticipants->contains($lessonStudent)) {
            $this->lessonParticipants->add($lessonStudent);
            $lessonStudent->setLesson($this);
        }

        return $this;
    }

    public function removeLessonParticipant(LessonParticipant $lessonStudent): static
    {
        if ($this->lessonParticipants->removeElement($lessonStudent)) {
            if ($lessonStudent->getLesson() === $this) {
                $lessonStudent->setLesson(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): void
    {
        $this->price = $price;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getIsParticipant(User $user): bool
    {
        return $this->getLessonParticipants()->exists(fn(int $key, LessonParticipant $lessonParticipant): bool => $lessonParticipant->getOwner() === $user);
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setLesson($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getLesson() === $this) {
                $comment->setLesson(null);
            }
        }

        return $this;
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

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }
}
