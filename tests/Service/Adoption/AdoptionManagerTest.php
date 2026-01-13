<?php

namespace App\Tests\Service\Adoption;

use App\Entity\Pet;
use App\Entity\AdoptionRequest;
use App\Entity\AdoptionHistory;
use App\Service\Adoption\AdoptionManager;
use App\Domain\Adoption\AdoptionStatus;
use App\Domain\Pets\PetStatus;
use PHPUnit\Framework\TestCase;

class AdoptionManagerTest extends TestCase
{
    public function test_approving_one_request_rejects_all_other_pending_requests(): void
    {
        // --- Arrange ---------------------------------------------------------

        $pet = new Pet();
        $pet->setName('Buddy');

        $requestA = new AdoptionRequest();
        $requestA->setPet($pet);

        $requestB = new AdoptionRequest();
        $requestB->setPet($pet);

        // simulate bidirectional relation
        $pet->getAdoptionRequests()->add($requestA);
        $pet->getAdoptionRequests()->add($requestB);

        $entityManager = new InMemoryEntityManager();
        $manager = new AdoptionManager($entityManager);

        // --- Act -------------------------------------------------------------

        $manager->approveRequest($requestA);

        // --- Assert ----------------------------------------------------------

        $histories = $entityManager->getPersisted(AdoptionHistory::class);

        // one approved + one rejected
        $this->assertCount(2, $histories);

        $statuses = array_map(
            fn (AdoptionHistory $h) => $h->getStatus(),
            $histories
        );

        $this->assertContains(AdoptionStatus::APPROVED, $statuses);
        $this->assertContains(AdoptionStatus::REJECTED, $statuses);
    }

    public function test_completing_request_archives_pet_and_writes_completed_history(): void
    {
        // --- Arrange ---------------------------------------------------------

        $pet = new Pet();
        $pet->setName('Buddy');

        $request = new AdoptionRequest();
        $request->setPet($pet);

        // simulate approved history
        $approvedHistory = new AdoptionHistory();
        $approvedHistory->setAdoptionRequest($request);
        $approvedHistory->setStatus(AdoptionStatus::APPROVED);

        $request->addAdoptionHistory($approvedHistory);

        $entityManager = new InMemoryEntityManager();
        $manager = new AdoptionManager($entityManager);

        // --- Act -------------------------------------------------------------

        $manager->completeRequest($request);

        // --- Assert ----------------------------------------------------------

        // Pet must be archived
        $this->assertSame(
            PetStatus::ARCHIVED,
            $pet->getStatus()
        );

        // Completed history must exist
        $histories = $entityManager->getPersisted(AdoptionHistory::class);

        $this->assertCount(1, $histories);

        $this->assertSame(
            AdoptionStatus::COMPLETED,
            $histories[0]->getStatus()
        );
    }
}
