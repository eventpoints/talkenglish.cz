<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class WordAssociationService
{
    public function __construct(
        private HttpClientInterface $datamuseClient
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function isRelatedWord(string $currentWord, string $potentiallyRelatedWord): bool
    {
        $response = $this->datamuseClient->request(
            method: Request::METHOD_GET,
            url: '/words',
            options: [
            'query' => [
                'rel_trg' => $currentWord,
            ],
        ]);

        $relatedWords = $response->toArray();

        foreach ($relatedWords as $relatedWord) {
            if (strtolower((string) $relatedWord['word']) === strtolower($potentiallyRelatedWord)) {
                return true;
            }
        }

        return false;
    }
}
