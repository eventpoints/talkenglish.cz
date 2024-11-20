<?php

declare(strict_types=1);

namespace App\Controller\Controller;

use App\DataTransferObject\PaymentAmountDto;
use App\Entity\Lesson;
use App\Service\StripePaymentService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/payment')]
class PaymentController extends AbstractController
{


    public function __construct(
        private readonly StripePaymentService $stripePaymentService,
    )
    {
    }

    #[Route('/options/{id}', name: 'select_payment_option', methods: [Request::METHOD_GET])]
    public function payment(Lesson $lesson): Response
    {
        return $this->render('payment/checkout.html.twig',[
            'lesson' => $lesson
        ]);
    }

    #[Route('/pay/{id}', name: 'pay_for_lesson', methods: [Request::METHOD_POST])]
    public function pay(Lesson $lesson, Request $request): JsonResponse
    {
        $payForLessonToken = $request->headers->get('X-CSRF-Token');
        if (!$this->isCsrfTokenValid('pay_for_lesson_token', $payForLessonToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        $paymentAmountDto = new PaymentAmountDto(amount: $lesson->getPrice(), currency: $lesson->getCurrency());
        try {
            $paymentIntentDto = $this->stripePaymentService->createPaymentIntent(paymentAmountDto: $paymentAmountDto);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Failed to create PaymentIntent'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'clientSecret' => $paymentIntentDto->getClientSecret(),
            'dpmCheckerLink' => "https://dashboard.stripe.com/settings/payment_methods/review?transaction_id={$paymentIntentDto->getPaymentIntentId()}"
        ]);
    }

}
