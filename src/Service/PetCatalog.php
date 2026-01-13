<?php

namespace App\Service;

use App\Entity\Pet;
use App\Repository\PetRepository;
use App\Domain\Matchmaker\MatchResult;
use App\Service\Matchmaker\Matchmaker;

/**
 * Orchestrates pet-related use cases.
 *
 * This service coordinates repositories and domain services
 * but does not contain business rules itself.
 */
class PetCatalog
{
    /**
     * @param array{weights: array<string,int>} $matchmakerConfig
     */
    public function __construct(
        private PetRepository $petRepository,
        private Matchmaker $matchmaker,
        private array $matchmakerConfig
    ) {
    }

    /**
     * Returns all fostered pets.
     *
     * @return Pet[]
     */
    public function getFosteredPets(): array
    {
        return $this->petRepository->findFosteredPets();
    }

    /**
     * Returns all adopted pets.
     *
     * @return Pet[]
     */
    public function getAdoptedPets(): array
    {
        return $this->petRepository->findAdoptedPets();
    }

    /**
     * Returns the current pet of the week.
     */
    public function getPetOfWeek(): ?Pet
    {
        return $this->petRepository->findPetOfWeek();
    }

    /**
     * Finds the best matching pet based on user preferences.
     *
     * Uses a scoring-based matchmaker and returns
     * an explainable domain result.
     */
    public function findMatch(
        ?string $activity,
        ?string $home,
        ?string $experience,
        ?string $petType,
        ?string $agePref
    ): ?MatchResult {
        // Load pets eligible for matchmaking
        $pets = $this->petRepository->findMatchCandidates();

        // Normalize user preferences
        $criteria = [
            'activity'   => $activity,
            'home'       => $home,
            'experience' => $experience,
            'petType'    => $petType,
            'agePref'    => $agePref,
        ];

        // Delegate decision-making to the Matchmaker
        return $this->matchmaker->match(
            pets: $pets,
            criteria: $criteria,
            weights: $this->matchmakerConfig['weights']
        );
    }

    /**
     * Returns all pets.
     *
     * @return Pet[]
     */
    public function getAll(): array
    {
        return $this->petRepository->findAllPets();
    }

    /**
     * Returns a single pet by its ID.
     */
    public function getById(int $id): ?Pet
    {
        return $this->petRepository->find($id);
    }
}
