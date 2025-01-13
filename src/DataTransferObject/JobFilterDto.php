<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use App\Enum\Job\EmploymentTypeEnum;
use App\Enum\Job\PaymentFrequencyEnum;

class JobFilterDto
{

    private null|string $keyword = null;
    private null|string|int $salary = null;
    private null|string $currency = null;
    private null|EmploymentTypeEnum $employmentTypeEnum = null;
    private null|PaymentFrequencyEnum $paymentFrequencyEnum = null;
    private null|string $country = null;
    private null|string $city = null;

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(?string $keyword): void
    {
        $this->keyword = $keyword;
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

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getSalary(): int|string|null
    {
        return $this->salary;
    }

    public function setSalary(int|string|null $salary): void
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

    public function getPaymentFrequencyEnum(): ?PaymentFrequencyEnum
    {
        return $this->paymentFrequencyEnum;
    }

    public function setPaymentFrequencyEnum(?PaymentFrequencyEnum $paymentFrequencyEnum): void
    {
        $this->paymentFrequencyEnum = $paymentFrequencyEnum;
    }

}
