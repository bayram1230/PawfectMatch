<?php

namespace App\Service\Adoption;

use App\Domain\Adoption\AdoptionRequest as DomainAdoptionRequest;
use App\Domain\Adoption\AdoptionStatus;
use App\Domain\Adoption\RejectionReason;
use App\Entity\AdoptionRequest;
use App\Entity\AdoptionHistory;
use LogicException;

class AdoptionManager
{
    public function __construct(
        private AdoptionEntityManager $entityManager
    ) {}

    /**
     * Step 1: Approve one request and reject all others.
     */
    public function approveRequest(AdoptionRequest $approvedRequest): void
    {
        $pet = $approvedRequest->getPet();

        $domainRequests = [];
        $domainApprovedRequest = null;

        foreach ($pet->getAdoptionRequests() as $request) {
            $domainRequest = DomainAdoptionRequest::create();

            if ($request === $approvedRequest) {
                $domainApprovedRequest = $domainRequest;
            }

            $domainRequests[spl_object_id($request)] = $domainRequest;
        }

        if (!$domainApprovedRequest) {
            throw new LogicException('Approved request not found.');
        }

        $otherDomainRequests = array_filter(
            $domainRequests,
            fn ($r) => $r !== $domainApprovedRequest
        );

        // Let the DOMAIN decide
        $domainApprovedRequest->approve($otherDomainRequests);

        foreach ($pet->getAdoptionRequests() as $request) {
            $domainRequest = $domainRequests[spl_object_id($request)];

            if ($domainRequest->getStatus() === AdoptionStatus::APPROVED) {
                $this->recordHistory(
                    $request,
                    AdoptionStatus::APPROVED,
                    null
                );
            }

            if ($domainRequest->getStatus() === AdoptionStatus::REJECTED) {
                $this->recordHistory(
                    $request,
                    AdoptionStatus::REJECTED,
                    RejectionReason::PET_ALREADY_ADOPTED
                );
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Step 2: Complete an approved adoption and archive the pet.
     */
    public function completeRequest(AdoptionRequest $request): void
    {
        // 🔑 Synchronize Entity → Domain
        $domainRequest = DomainAdoptionRequest::fromStatus(
            $request->getLatestStatus(),
            $request->getLatestRejectionReason()
        );

        // Domain validates legality
        $domainRequest->complete();

        // Persist completion
        $this->recordHistory(
            $request,
            AdoptionStatus::COMPLETED,
            null
        );

        // Archive pet as a CONSEQUENCE
        $request->getPet()->archive();

        $this->entityManager->flush();
    }

    private function recordHistory(
        AdoptionRequest $request,
        string $status,
        ?string $rejectionReason
    ): void {
        $history = new AdoptionHistory();
        $history->setAdoptionRequest($request);
        $history->setStatus($status);

        if ($rejectionReason !== null && method_exists($history, 'setRejectionReason')) {
            $history->setRejectionReason($rejectionReason);
        }

        $this->entityManager->persist($history);
    }
}
