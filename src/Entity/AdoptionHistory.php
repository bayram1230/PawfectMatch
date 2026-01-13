<?php

namespace App\Entity;

use App\Repository\AdoptionHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdoptionHistoryRepository::class)]
class AdoptionHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'adoptionHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AdoptionRequest $adoptionRequest = null;

    #[ORM\Column(length: 20)]
    private string $status;

    #[ORM\Column]
    private \DateTimeImmutable $decidedAt;

    #[ORM\ManyToOne]
    private ?User $decidedBy = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $rejectionReason = null;

    public function __construct()
    {
        $this->decidedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdoptionRequest(): ?AdoptionRequest
    {
        return $this->adoptionRequest;
    }

    public function setAdoptionRequest(?AdoptionRequest $adoptionRequest): static
    {
        $this->adoptionRequest = $adoptionRequest;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getDecidedAt(): \DateTimeImmutable
    {
        return $this->decidedAt;
    }

    public function setDecidedAt(\DateTimeImmutable $decidedAt): static
    {
        $this->decidedAt = $decidedAt;
        return $this;
    }

    public function getDecidedBy(): ?User
    {
        return $this->decidedBy;
    }

    public function setDecidedBy(?User $decidedBy): static
    {
        $this->decidedBy = $decidedBy;
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;
        return $this;
    }

    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    public function setRejectionReason(?string $rejectionReason): static
    {
        $this->rejectionReason = $rejectionReason;
        return $this;
    }
}
