<?php

namespace App\Tests\Domain\Adoption;

use PHPUnit\Framework\TestCase;
use App\Domain\Adoption\AdoptionRequest;
use App\Domain\Adoption\AdoptionStatus;
use App\Domain\Adoption\RejectionReason;
use DomainException;

class AdoptionRequestTest extends TestCase
{
    public function test_new_adoption_request_starts_in_pending_state(): void
    {
        $request = AdoptionRequest::create();

        $this->assertSame(
            AdoptionStatus::PENDING,
            $request->getStatus()
        );
    }

    public function test_pending_request_cannot_be_directly_adopted(): void
    {
        $request = AdoptionRequest::create();

        $this->expectException(DomainException::class);

        $request->markAsAdopted();
    }

    public function test_pending_request_can_be_approved(): void
    {
        $request = AdoptionRequest::create();

        $request->approve();

        $this->assertSame(
            AdoptionStatus::APPROVED,
            $request->getStatus()
        );
    }

    public function test_approved_request_cannot_be_set_back_to_pending(): void
    {
        $request = AdoptionRequest::create();

        $request->approve();

        $this->expectException(DomainException::class);

        $request->markAsPending();
    }

    public function test_approving_one_request_rejects_all_other_pending_requests_for_same_pet(): void
    {
        $requestA = AdoptionRequest::create();
        $requestB = AdoptionRequest::create();

        $requestA->approve([$requestB]);

        $this->assertSame(
            AdoptionStatus::REJECTED,
            $requestB->getStatus()
        );

        $this->assertSame(
            RejectionReason::PET_ALREADY_ADOPTED,
            $requestB->getRejectionReason()
        );
    }

    /* -------------------------------------------------
     * NEW TESTS: Completion lifecycle
     * ------------------------------------------------- */

    public function test_approved_request_can_be_completed(): void
    {
        $request = AdoptionRequest::fromStatus(
            AdoptionStatus::APPROVED
        );

        $request->complete();

        $this->assertSame(
            AdoptionStatus::COMPLETED,
            $request->getStatus()
        );
    }

    public function test_pending_request_cannot_be_completed(): void
    {
        $request = AdoptionRequest::create();

        $this->expectException(DomainException::class);

        $request->complete();
    }

    public function test_rejected_request_cannot_be_completed(): void
    {
        $request = AdoptionRequest::fromStatus(
            AdoptionStatus::REJECTED,
            RejectionReason::PET_ALREADY_ADOPTED
        );

        $this->expectException(DomainException::class);

        $request->complete();
    }
}
