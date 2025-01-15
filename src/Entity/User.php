<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\AccountTypeEnum;
use App\Enum\Quiz\LevelEnum;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private null|Uuid $id = null;

    #[ORM\Column(length: 180)]
    private ?string $firstName = null;

    #[ORM\Column(length: 180)]
    private ?string $lastName = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private null|string $avatar = null;

    #[ORM\Column(nullable: true, enumType: LevelEnum::class)]
    private null|LevelEnum $levelEnum = LevelEnum::A1;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: true)]
    private null|string $password;

    /**
     * @var Collection<int, QuizParticipation>
     */
    #[ORM\OneToMany(targetEntity: QuizParticipation::class, mappedBy: 'owner', cascade: ['remove'])]
    private Collection $quizParticipations;

    #[ORM\Column(length: 255, nullable: true, options: ["default" => "STUDENT"])]
    private null|AccountTypeEnum $accountTypeEnum = AccountTypeEnum::STUDENT;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $timezone = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private null|CarbonImmutable $levelAssessmentQuizTakenAt = null;

    #[ORM\Column(nullable: true)]
    private null|bool $isSubscribedToWeeklyQuizEmail = true;

    /**
     * @var Collection<int, EmailTransmission>
     */
    #[ORM\OneToMany(targetEntity: EmailTransmission::class, mappedBy: 'owner')]
    private Collection $emailTransmissions;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $fingerprint = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private CarbonImmutable $createdAt;

    /**
     * @var Collection<int, JobAdvertisement>
     */
    #[ORM\OneToMany(targetEntity: JobAdvertisement::class, mappedBy: 'owner')]
    private Collection $jobAdvertisements;

    /**
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $avatar
     * @param string|null $fingerprint
     * @param string|null $password
     * @param string|null $email
     */
    public function __construct(?string $firstName = null, ?string $lastName = null, ?string $avatar = null, ?string $fingerprint = null, ?string $password = null, ?string $email = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->avatar = $avatar;
        $this->email = $email;
        $this->password = $password;
        $this->fingerprint = $fingerprint;
        $this->quizParticipations = new ArrayCollection();
        $this->emailTransmissions = new ArrayCollection();
        $this->createdAt = new CarbonImmutable();
        $this->jobAdvertisements = new ArrayCollection();
    }


    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = RoleEnum::USER->value;

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): null|string
    {
        return $this->password;
    }

    public function setPassword(null|string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
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
            $quizParticipation->setOwner($this);
        }

        return $this;
    }

    public function removeQuizParticipation(QuizParticipation $quizParticipation): static
    {
        if ($this->quizParticipations->removeElement($quizParticipation)) {
            // set the owning side to null (unless already changed)
            if ($quizParticipation->getOwner() === $this) {
                $quizParticipation->setOwner(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getLevelEnum(): ?LevelEnum
    {
        return $this->levelEnum;
    }

    public function setLevelEnum(?LevelEnum $levelEnum): void
    {
        $this->levelEnum = $levelEnum;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getLevelAssessmentQuizTakenAt(): null|CarbonImmutable
    {
        return $this->levelAssessmentQuizTakenAt;
    }

    public function setLevelAssessmentQuizTakenAt(null|CarbonImmutable $levelAssessmentQuizTakenAt): static
    {
        $this->levelAssessmentQuizTakenAt = $levelAssessmentQuizTakenAt;

        return $this;
    }

    public function daysUntilPermittedToRetakeLevelAssessmentQuiz(): int
    {
        return (int) CarbonImmutable::now()->diffInDays($this->getLevelAssessmentQuizTakenAt()->addDays(30));
    }

    public function canRetakeLevelAssessmentQuiz(): bool
    {
        if (!$this->getLevelAssessmentQuizTakenAt() instanceof CarbonImmutable) {
            return true;
        }

        return (new CarbonImmutable()) > $this->getLevelAssessmentQuizTakenAt()->addMonths(3);
    }

    public function isSubscribedToWeeklyQuizEmail(): null|bool
    {
        return $this->isSubscribedToWeeklyQuizEmail;
    }

    public function setSubscribedToWeeklyQuizEmail(null|bool $isSubscribedToWeeklyQuizEmail): static
    {
        $this->isSubscribedToWeeklyQuizEmail = $isSubscribedToWeeklyQuizEmail;

        return $this;
    }

    /**
     * @return Collection<int, EmailTransmission>
     */
    public function getEmailTransmissions(): Collection
    {
        return $this->emailTransmissions;
    }

    public function addEmailTransmission(EmailTransmission $emailTransmission): static
    {
        if (!$this->emailTransmissions->contains($emailTransmission)) {
            $this->emailTransmissions->add($emailTransmission);
            $emailTransmission->setOwner($this);
        }

        return $this;
    }

    public function removeEmailTransmission(EmailTransmission $emailTransmission): static
    {
        if ($this->emailTransmissions->removeElement($emailTransmission)) {
            // set the owning side to null (unless already changed)
            if ($emailTransmission->getOwner() === $this) {
                $emailTransmission->setOwner(null);
            }
        }

        return $this;
    }

    public function getFingerprint(): ?string
    {
        return $this->fingerprint;
    }

    public function setFingerprint(?string $fingerprint): static
    {
        $this->fingerprint = $fingerprint;

        return $this;
    }

    public function getIsSubscribedToWeeklyQuizEmail(): ?bool
    {
        return $this->isSubscribedToWeeklyQuizEmail;
    }

    public function setIsSubscribedToWeeklyQuizEmail(?bool $isSubscribedToWeeklyQuizEmail): void
    {
        $this->isSubscribedToWeeklyQuizEmail = $isSubscribedToWeeklyQuizEmail;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CarbonImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getDaysRemainingUntilGuestAccountDelation(): int
    {
        return (int) CarbonImmutable::now()->diffInDays($this->getCreatedAt()->addDays(30));
    }

    /**
     * @return Collection<int, JobAdvertisement>
     */
    public function getJobAdvertisements(): Collection
    {
        return $this->jobAdvertisements;
    }

    public function addJobAdvertisement(JobAdvertisement $jobAdvertisement): static
    {
        if (!$this->jobAdvertisements->contains($jobAdvertisement)) {
            $this->jobAdvertisements->add($jobAdvertisement);
            $jobAdvertisement->setOwner($this);
        }

        return $this;
    }

    public function removeJobAdvertisement(JobAdvertisement $jobAdvertisement): static
    {
        if ($this->jobAdvertisements->removeElement($jobAdvertisement)) {
            // set the owning side to null (unless already changed)
            if ($jobAdvertisement->getOwner() === $this) {
                $jobAdvertisement->setOwner(null);
            }
        }

        return $this;
    }

    public function getAccountTypeEnum(): ?AccountTypeEnum
    {
        return $this->accountTypeEnum;
    }

    public function setAccountTypeEnum(?AccountTypeEnum $accountTypeEnum): void
    {
        $this->accountTypeEnum = $accountTypeEnum;
    }

}
