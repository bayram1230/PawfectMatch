<?php

namespace App\Domain\Matchmaker;

use App\Entity\Pet;

/**
 * Represents the result of a matchmaking decision.
 *
 * This is a domain object (value object).
 * It contains no logic, no persistence, and no framework dependencies.
 */
class MatchResult
{
    /**
     * @param MatchReason[] $reasons Structured reasons explaining the match
     */
    public function __construct(
        private Pet $pet,
        private int $score,
        private array $reasons
    ) {
    }

    /**
     * The matched pet.
     */
    public function getPet(): Pet
    {
        return $this->pet;
    }

    /**
     * The calculated match score.
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * Reasons explaining why this pet was selected.
     *
     * @return MatchReason[]
     */
    public function getReasons(): array
    {
        return $this->reasons;
    }
}
