<?php

namespace App\Service\Matchmaker;

use App\Entity\Pet;
use App\Domain\Matchmaker\MatchResult;
use App\Domain\Matchmaker\MatchReason;

/**
 * Scoring-based matchmaker.
 *
 * This service evaluates a list of pets against user preferences
 * and returns the best matching pet with an explanation.
 *
 * Responsibilities:
 * - Apply matching rules
 * - Calculate scores
 * - Explain why a pet was selected
 *
 * This class is framework-agnostic and does not access the database.
 */
class Matchmaker
{
    /**
     * Find the best matching pet based on criteria and weights.
     *
     * @param Pet[] $pets
     * @param array $criteria
     * @param array $weights
     */
    public function match(
        array $pets,
        array $criteria,
        array $weights
    ): ?MatchResult {
        $bestResult = null;

        foreach ($pets as $pet) {
            [$score, $reasons] = $this->scorePet($pet, $criteria, $weights);

            if ($bestResult === null || $score > $bestResult->getScore()) {
                $bestResult = new MatchResult(
                    pet: $pet,
                    score: $score,
                    reasons: $reasons
                );
            }
        }

        return $bestResult;
    }

    /**
     * Calculate the score for a single pet.
     *
     * @return array{0:int,1:MatchReason[]}
     */
    private function scorePet(
        Pet $pet,
        array $criteria,
        array $weights
    ): array {
        $reasons = [];

        // Preferred pet type
        if (
            !empty($criteria['petType']) &&
            $pet->getSpecies() === $criteria['petType']
        ) {
            $reasons[] = new MatchReason(
                code: 'pet_type_match',
                weight: $weights['pet_type']
            );
        }

        // Activity level and home type compatibility
        if (
            $criteria['activity'] === 'active' &&
            $criteria['home'] === 'house' &&
            $pet->getSize() === 'Large'
        ) {
            $reasons[] = new MatchReason(
                code: 'active_house_large_pet',
                weight: $weights['activity_home']
            );
        }

        if (
            $criteria['activity'] === 'calm' &&
            $criteria['home'] === 'apartment' &&
            $pet->getSize() === 'Small'
        ) {
            $reasons[] = new MatchReason(
                code: 'calm_apartment_small_pet',
                weight: $weights['activity_home']
            );
        }

        // Experience level suitability
        if (
            $criteria['experience'] === 'first' &&
            $pet->getAge() !== null &&
            $pet->getAge() <= 4
        ) {
            $reasons[] = new MatchReason(
                code: 'first_time_owner_suitable',
                weight: $weights['experience']
            );
        }

        if (
            $criteria['experience'] === 'experienced' &&
            $pet->getAge() !== null &&
            $pet->getAge() >= 5
        ) {
            $reasons[] = new MatchReason(
                code: 'experienced_owner_suitable',
                weight: $weights['experience']
            );
        }

        // Age preference
        if (
            $criteria['agePref'] === 'young' &&
            $pet->getAge() !== null &&
            $pet->getAge() <= 2
        ) {
            $reasons[] = new MatchReason(
                code: 'young_pet_preference',
                weight: $weights['age_preference']
            );
        }

        if (
            $criteria['agePref'] === 'senior' &&
            $pet->getAge() !== null &&
            $pet->getAge() >= 8
        ) {
            $reasons[] = new MatchReason(
                code: 'senior_pet_preference',
                weight: $weights['age_preference']
            );
        }

        $score = array_sum(
            array_map(
                static fn (MatchReason $reason) => $reason->getWeight(),
                $reasons
            )
        );

        return [$score, $reasons];
    }
}
