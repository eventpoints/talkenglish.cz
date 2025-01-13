<?php

namespace App\Entity;

use App\Enum\Job\EmploymentTypeEnum;
use App\Enum\Job\PaymentFrequencyEnum;
use App\Enum\Job\SalaryRangeEnum;
use App\Repository\JobAdvertisementRepository;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: JobAdvertisementRepository::class)]
class JobAdvertisement
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private null|Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(enumType: EmploymentTypeEnum::class)]
    private null|EmploymentTypeEnum $employmentTypeEnum = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isRelocationIncluded = false;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column]
    private null|int $salary = null;

    #[ORM\Column(length: 3)]
    private null|string $currency = null;

    #[ORM\Column(enumType: PaymentFrequencyEnum::class)]
    private null|PaymentFrequencyEnum $paymentFrequencyEnum = null;

    #[ORM\Column(nullable: true)]
    private null|DateTimeImmutable $publishedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private CarbonImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'jobAdvertisements')]
    private null|User $owner = null;

    #[ORM\Column(length: 255)]
    private ?string $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $applicationUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $applicationEmailAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ApplicationPhoneNumber = null;

    /**
     * @param User|null $owner
     */
    public function __construct(null|User $owner = null)
    {
        $this->owner = $owner;
        $this->createdAt = new CarbonImmutable();
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

    public function getEmploymentTypeEnum(): ?EmploymentTypeEnum
    {
        return $this->employmentTypeEnum;
    }

    public function setEmploymentTypeEnum(?EmploymentTypeEnum $employmentTypeEnum): void
    {
        $this->employmentTypeEnum = $employmentTypeEnum;
    }
    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getSalary(): ?int
    {
        return $this->salary;
    }

    public function setSalary(?int $salary): void
    {
        $this->salary = $salary;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }


    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getApplicationUrl(): ?string
    {
        return $this->applicationUrl;
    }

    public function setApplicationUrl(?string $applicationUrl): static
    {
        $this->applicationUrl = $applicationUrl;

        return $this;
    }

    public function getApplicationEmailAddress(): ?string
    {
        return $this->applicationEmailAddress;
    }

    public function setApplicationEmailAddress(?string $applicationEmailAddress): static
    {
        $this->applicationEmailAddress = $applicationEmailAddress;

        return $this;
    }

    public function getApplicationPhoneNumber(): ?string
    {
        return $this->ApplicationPhoneNumber;
    }

    public function setApplicationPhoneNumber(?string $ApplicationPhoneNumber): static
    {
        $this->ApplicationPhoneNumber = $ApplicationPhoneNumber;

        return $this;
    }

    public function getPaymentFrequencyEnum(): ?PaymentFrequencyEnum
    {
        return $this->paymentFrequencyEnum;
    }

    public function setPaymentFrequencyEnum(?PaymentFrequencyEnum $paymentFrequencyEnum): void
    {
        $this->paymentFrequencyEnum = $paymentFrequencyEnum;
    }

    public function isRelocationIncluded(): bool
    {
        return $this->isRelocationIncluded;
    }

    public function setIsRelocationIncluded(bool $isRelocationIncluded): void
    {
        $this->isRelocationIncluded = $isRelocationIncluded;
    }

}
