<?php

namespace App\Domain\Matchmaker;

/**
 * Represents a single reason why a pet matched.
 *
 * This is a domain value object.
 */
final class MatchReason
{
    public function __construct(
        private string $code,
        private int $weight
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }
}
