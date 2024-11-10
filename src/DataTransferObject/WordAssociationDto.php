<?php

namespace App\DataTransferObject;

final class WordAssociationDto
{
    /**
     * @param string|null $potentiallyRelatedWord
     * @param string $currentWord
     */
    public function __construct(private string $currentWord, private null|string $potentiallyRelatedWord = null)
    {
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
