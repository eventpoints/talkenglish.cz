<?php

namespace App\DataTransferObject;

final class WordAssociationDto
{
    private null|string $potentiallyRelatedWord;
    private string $currentWord;

    /**
     * @param string|null $potentiallyRelatedWord
     * @param string $currentWord
     */
    public function __construct(string $currentWord, string $potentiallyRelatedWord = null)
    {
        $this->potentiallyRelatedWord = $potentiallyRelatedWord;
        $this->currentWord = $currentWord;
    }

    public function getPotentiallyRelatedWord(): ?string
    {
        return $this->potentiallyRelatedWord;
    }

    public function setPotentiallyRelatedWord(?string $potentiallyRelatedWord): void
    {
        $this->potentiallyRelatedWord = $potentiallyRelatedWord;
    }

    public function getCurrentWord(): string
    {
        return $this->currentWord;
    }

    public function setCurrentWord(string $currentWord): void
    {
        $this->currentWord = $currentWord;
    }

}
