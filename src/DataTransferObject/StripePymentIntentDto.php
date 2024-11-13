<?php

namespace App\DataTransferObject;

final readonly class StripePymentIntentDto
{

    private string $clientSecret;
    private string $paymentIntentId;

    /**
     * @param string $clientSecret
     * @param string $paymentIntentId
     */
    public function __construct(string $clientSecret, string $paymentIntentId)
    {
        $this->clientSecret = $clientSecret;
        $this->paymentIntentId = $paymentIntentId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getPaymentIntentId(): string
    {
        return $this->paymentIntentId;
    }

}