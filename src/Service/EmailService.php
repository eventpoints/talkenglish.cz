<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Quiz;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class EmailService
{
    private const SENDER_EMAIL_ADDRESS = 'no-reply@customizer.site';

    public function __construct(
        private MailerInterface $mailer,
        private TranslatorInterface $translator
    ) {
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    public function sendQuizEmail(string $emailAddress, Quiz $quiz, array $context = []): void
    {
        $this->send(
            subject: $this->translator->trans('email.weekly-quiz-subject', ['title' => $quiz->getTitle()]),
            template: '/email/quiz.html.twig',
            emailAddress: $emailAddress,
            context: $context
        );
    }

    /**
     * @param array<string|int|object> $context
     */
    private function compose(
        string $subject,
        string $template,
        string $emailAddress,
        array $context
    ): TemplatedEmail {
        $templatedEmail = new TemplatedEmail();
        $templatedEmail->from(addresses: self::SENDER_EMAIL_ADDRESS);
        $templatedEmail->to(address: new Address($emailAddress));
        $templatedEmail->subject(subject: $subject);
        $templatedEmail->htmlTemplate(template: $template);
        $templatedEmail->context(context: $context);
        return $templatedEmail;
    }

    /**
     * @param array<string|int|object> $context
     * @throws TransportExceptionInterface
     */
    private function send(
        string $subject,
        string $template,
        string $emailAddress,
        array $context
    ): void {
        try {
            $envelope = $this->compose(
                subject: $this->translator->trans($subject),
                template: $template,
                emailAddress: $emailAddress,
                context: $context
            );
            $this->mailer->send($envelope);
        } catch (TransportExceptionInterface $transportException) {
            throw new $transportException();
        }
    }
}
