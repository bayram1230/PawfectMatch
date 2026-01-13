<?php

namespace App\Tests\Service\Matchmaker;

use App\Entity\Pet;
use App\Service\Matchmaker\Matchmaker;
use PHPUnit\Framework\TestCase;

class MatchmakerTest extends TestCase
{
    private Matchmaker $matchmaker;

    protected function setUp(): void
    {
        $this->matchmaker = new Matchmaker();
    }

    public function test_it_selects_the_best_matching_pet(): void
    {
        $pets = [
            $this->createPet('Bella', 'Dog', 'Large', 3),
            $this->createPet('Milo', 'Cat', 'Small', 5),
        ];

        $criteria = [
            'activity'   => 'active',
            'home'       => 'house',
            'experience' => 'first',
            'petType'    => 'Dog',
            'agePref'    => 'young',
        ];

        $weights = $this->defaultWeights();

        $result = $this->matchmaker->match($pets, $criteria, $weights);

        $this->assertNotNull($result);
        $this->assertSame('Bella', $result->getPet()->getName());
        $this->assertGreaterThan(0, $result->getScore());
        $this->assertNotEmpty($result->getReasons());
    }

    public function test_it_returns_null_when_no_pets_are_provided(): void
    {
        $result = $this->matchmaker->match([], [], []);

        $this->assertNull($result);
    }

    public function test_it_returns_zero_score_when_no_criteria_are_given(): void
    {
        $pets = [
            $this->createPet('Bella', 'Dog', 'Large', 3),
        ];

        $criteria = [
            'activity'   => null,
            'home'       => null,
            'experience' => null,
            'petType'    => null,
            'agePref'    => null,
        ];

        $result = $this->matchmaker->match($pets, $criteria, $this->defaultWeights());

        $this->assertNotNull($result);
        $this->assertSame(0, $result->getScore());
        $this->assertSame([], $result->getReasons());
    }

    public function test_it_returns_the_first_pet_when_multiple_pets_have_the_same_score(): void
    {
        $pets = [
            $this->createPet('Bella', 'Dog', 'Large', 3),
            $this->createPet('Max', 'Dog', 'Large', 3),
        ];

        $criteria = [
            'activity'   => null,
            'home'       => null,
            'experience' => null,
            'petType'    => null,
            'agePref'    => null,
        ];

        $result = $this->matchmaker->match($pets, $criteria, $this->defaultWeights());

        $this->assertNotNull($result);
        $this->assertSame('Bella', $result->getPet()->getName());
        $this->assertSame(0, $result->getScore());
    }

    public function test_it_always_provides_reasons_when_score_is_greater_than_zero(): void
    {
        $pets = [
            $this->createPet('Bella', 'Dog', 'Large', 3),
        ];

        $criteria = [
            'activity'   => null,
            'home'       => null,
            'experience' => null,
            'petType'    => 'Dog',
            'agePref'    => null,
        ];

        $result = $this->matchmaker->match($pets, $criteria, $this->defaultWeights());

        $this->assertNotNull($result);
        $this->assertGreaterThan(0, $result->getScore());
        $this->assertNotEmpty($result->getReasons());
    }

    public function test_it_explains_pet_type_match_with_a_specific_reason(): void
    {
        $pets = [
            $this->createPet('Bella', 'Dog', 'Large', 3),
        ];

        $criteria = [
            'activity'   => null,
            'home'       => null,
            'experience' => null,
            'petType'    => 'Dog',
            'agePref'    => null,
        ];

        $result = $this->matchmaker->match($pets, $criteria, $this->defaultWeights());

        $codes = $this->extractReasonCodes($result->getReasons());

        $this->assertSame(25, $result->getScore());
        $this->assertContains('pet_type_match', $codes);
    }

    public function test_young_age_preference_includes_age_two(): void
    {
        $pets = [
            $this->createPet('Luna', 'Cat', 'Small', 2),
        ];

        $criteria = [
            'activity'   => null,
            'home'       => null,
            'experience' => null,
            'petType'    => null,
            'agePref'    => 'young',
        ];

        $result = $this->matchmaker->match($pets, $criteria, $this->defaultWeights());

        $codes = $this->extractReasonCodes($result->getReasons());

        $this->assertSame(15, $result->getScore());
        $this->assertContains('young_pet_preference', $codes);
    }

    public function test_first_time_owner_includes_age_four(): void
    {
        $pets = [
            $this->createPet('Buddy', 'Dog', 'Medium', 4),
        ];

        $criteria = [
            'activity'   => null,
            'home'       => null,
            'experience' => 'first',
            'petType'    => null,
            'agePref'    => null,
        ];

        $result = $this->matchmaker->match($pets, $criteria, $this->defaultWeights());

        $codes = $this->extractReasonCodes($result->getReasons());

        $this->assertSame(15, $result->getScore());
        $this->assertContains('first_time_owner_suitable', $codes);
    }

    public function test_conflicting_criteria_result_in_zero_score(): void
    {
        $pets = [
            $this->createPet('Oscar', 'Dog', 'Large', 9),
        ];

        $criteria = [
            'activity'   => null,
            'home'       => null,
            'experience' => 'first',
            'petType'    => null,
            'agePref'    => 'senior',
        ];

        $result = $this->matchmaker->match($pets, $criteria, $this->defaultWeights());

        $codes = $this->extractReasonCodes($result->getReasons());

        $this->assertSame(15, $result->getScore());
        $this->assertContains('senior_pet_preference', $codes);
    }

    /* ------------------------- helpers ------------------------- */

    private function extractReasonCodes(array $reasons): array
    {
        return array_map(
            static fn ($reason) => $reason->getCode(),
            $reasons
        );
    }

    private function defaultWeights(): array
    {
        return [
            'pet_type'       => 25,
            'activity_home'  => 20,
            'experience'     => 15,
            'age_preference' => 15,
        ];
    }

    private function createPet(
        string $name,
        string $species,
        string $size,
        ?int $age
    ): Pet {
        $pet = new Pet();
        $pet->setName($name);
        $pet->setSpecies($species);
        $pet->setSize($size);
        $pet->setAge($age);

        return $pet;
    }
}
