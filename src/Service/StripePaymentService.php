<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObject\PaymentAmountDto;
use App\DataTransferObject\StripePymentIntentDto;
use Stripe\StripeClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final readonly class StripePaymentService
{

    public function __construct(
        private ParameterBagInterface $parameterBag
    )
    {
    }

    public function createPaymentIntent(PaymentAmountDto $paymentAmountDto): StripePymentIntentDto
    {
        $stripe = new StripeClient($this->parameterBag->get('app.stripe_private_key'));
        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $paymentAmountDto->getAmount(),
            'currency' => $paymentAmountDto->getCurrency(),
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return new StripePymentIntentDto(
            clientSecret: $paymentIntent->client_secret,
            paymentIntentId: $paymentIntent->id
        );
    }

}
