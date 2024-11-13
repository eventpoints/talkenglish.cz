<?php

namespace App\Event\Subscriber;

use App\Entity\Lesson;
use App\Repository\LessonRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use Exception;
use Square\Models\AcceptedPaymentMethods;
use Square\Models\CheckoutOptions;
use Square\Models\CreatePaymentLinkRequest;
use Square\Models\CreatePaymentLinkResponse;
use Square\Models\Money;
use Square\Models\QuickPay;
use Square\SquareClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;


readonly class LessonSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private LessonRepository      $lessonRepository,
        private UrlGeneratorInterface $urlGenerator,
        private ParameterBagInterface $parameterBag
    )
    {
    }

    /**
     * @return array<string, array<string>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => ['postPersist'],
        ];
    }


    public function postPersist(AfterEntityPersistedEvent $args): void
    {
        $entity = $args->getEntityInstance();
        if (!$entity instanceof Lesson) {
            return;
        }

        $this->generatePaymentLink(lesson: $entity);
    }

    /**
     * @throws Exception
     */
    private function generatePaymentLink(Lesson $lesson): void
    {
        $config = [
            'accessToken' => $this->parameterBag->get('app.squareup_key'),
            'environment' =>  $this->parameterBag->get('app.env') == 'prod' ? 'production' : 'sandbox'
        ];

        $client = new SquareClient(config: $config);
        $price_money = new Money();
        $price_money->setAmount(amount: $lesson->getPrice());
        $price_money->setCurrency(currency: $lesson->getCurrency());

        $quick_pay = new QuickPay(
            name: $lesson->getTitle(),
            priceMoney: $price_money,
            locationId: $this->parameterBag->get('app.location_id')
        );

        $redirectUrl = $this->urlGenerator->generate(name: 'join_lesson_redirect', parameters: ['id' => $lesson->getId()], referenceType: UrlGeneratorInterface::ABSOLUTE_URL);

        $body = new CreatePaymentLinkRequest();
        $checkoutOptions = new CheckoutOptions();
        $checkoutOptions->setRedirectUrl($redirectUrl);
        $acceptedPaymentMethods = $this->getAcceptedPaymentMethods();
        $checkoutOptions->setAcceptedPaymentMethods(acceptedPaymentMethods: $acceptedPaymentMethods);
        $body->setCheckoutOptions(checkoutOptions: $checkoutOptions);
        $body->setIdempotencyKey(Uuid::v4());
        $body->setQuickPay($quick_pay);

        $apiResponse = $client->getCheckoutApi()->createPaymentLink($body);

        if ($apiResponse->isSuccess()) {
            /**
             * @var CreatePaymentLinkResponse $result
             */
            $result = $apiResponse->getResult();
            $lesson->setPaymentLink($result->getPaymentLink()->getUrl());
            $this->lessonRepository->save(entity: $lesson, flush: true);
        } else {
            dd($apiResponse->getErrors());
        }

    }

    private function getAcceptedPaymentMethods(): AcceptedPaymentMethods
    {
        $acceptedPaymentMethods = new AcceptedPaymentMethods();
        $acceptedPaymentMethods->getApplePay();
        $acceptedPaymentMethods->getGooglePay();
        return $acceptedPaymentMethods;
    }
}