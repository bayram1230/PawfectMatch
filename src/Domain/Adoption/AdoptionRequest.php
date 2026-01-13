<?php

namespace App\Domain\Adoption;

use DomainException;

class AdoptionRequest
{
    private string $status;
    private ?string $rejectionReason = null;

    private function __construct()
    {
        $this->status = AdoptionStatus::PENDING;
    }

    /**
     * Factory for new adoption requests (PENDING).
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Factory for existing adoption requests (rehydration from persistence).
     */
    public static function fromStatus(
        string $status,
        ?string $rejectionReason = null
    ): self {
        $self = new self();
        $self->status = $status;
        $self->rejectionReason = $rejectionReason;

        return $self;
    }

    /**
     * Approve this request and reject all others.
     *
     * @param self[] $otherRequests
     */
    public function approve(array $otherRequests = []): void
    {
        if ($this->status !== AdoptionStatus::PENDING) {
            throw new DomainException('Only pending requests can be approved.');
        }

        $this->status = AdoptionStatus::APPROVED;

        foreach ($otherRequests as $request) {
            $request->reject(RejectionReason::PET_ALREADY_ADOPTED);
        }
    }

    public function reject(string $reason): void
    {
        if ($this->status !== AdoptionStatus::PENDING) {
            return;
        }

        $this->status = AdoptionStatus::REJECTED;
        $this->rejectionReason = $reason;
    }

    /**
     * Complete an approved adoption.
     */
    public function complete(): void
    {
        if ($this->status !== AdoptionStatus::APPROVED) {
            throw new DomainException('Only approved requests can be completed.');
        }

        $this->status = AdoptionStatus::COMPLETED;
    }

    /**
     * Forbidden transitions (explicit by design)
     */
    public function markAsAdopted(): void
    {
        throw new DomainException('Use complete() to finalize an adoption.');
    }

    public function markAsPending(): void
    {
        throw new DomainException('Approved requests cannot return to pending.');
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }
}
