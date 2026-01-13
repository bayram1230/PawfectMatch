<?php

namespace App\Entity;

use App\Repository\AdoptionRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdoptionRequestRepository::class)]
class AdoptionRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'adoptionRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pet $pet = null;

    /**
     * The user who applied for the pet
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $applicant = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $surveyAnswer = null;

    /**
     * @var Collection<int, AdoptionHistory>
     */
    #[ORM\OneToMany(
        targetEntity: AdoptionHistory::class,
        mappedBy: 'adoptionRequest',
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['decidedAt' => 'ASC'])]
    private Collection $adoptionHistories;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->adoptionHistories = new ArrayCollection();
    }

    // -------------------------
    // Identity & Relations
    // -------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPet(): ?Pet
    {
        return $this->pet;
    }

    public function setPet(Pet $pet): self
    {
        $this->pet = $pet;
        return $this;
    }

    public function getApplicant(): ?User
    {
        return $this->applicant;
    }

    public function setApplicant(User $applicant): self
    {
        $this->applicant = $applicant;
        return $this;
    }

    // -------------------------
    // Applicant-provided data
    // -------------------------

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getSurveyAnswer(): ?string
    {
        return $this->surveyAnswer;
    }

    public function setSurveyAnswer(?string $surveyAnswer): self
    {
        $this->surveyAnswer = $surveyAnswer;
        return $this;
    }

    // -------------------------
    // Timestamps
    // -------------------------

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    // -------------------------
    // Adoption History
    // -------------------------

    /**
     * @return Collection<int, AdoptionHistory>
     */
    public function getAdoptionHistories(): Collection
    {
        return $this->adoptionHistories;
    }

    public function addAdoptionHistory(AdoptionHistory $adoptionHistory): self
    {
        if (!$this->adoptionHistories->contains($adoptionHistory)) {
            $this->adoptionHistories->add($adoptionHistory);
            $adoptionHistory->setAdoptionRequest($this);
        }

        return $this;
    }

    public function removeAdoptionHistory(AdoptionHistory $adoptionHistory): self
    {
        if ($this->adoptionHistories->removeElement($adoptionHistory)) {
            if ($adoptionHistory->getAdoptionRequest() === $this) {
                $adoptionHistory->setAdoptionRequest(null);
            }
        }

        return $this;
    }

    // -------------------------
    // Read-only helpers
    // -------------------------

    /**
     * Returns the current status based on the latest history entry
     */
    public function getCurrentStatus(): ?string
    {
        if ($this->adoptionHistories->isEmpty()) {
            return null;
        }

        return $this->adoptionHistories->last()->getStatus();
    }

    public function getLatestStatus(): ?string
    {
        return $this->getCurrentStatus();
    }

    public function getLatestRejectionReason(): ?string
    {
        $history = $this->getLatestHistory();

        return $history?->getRejectionReason();
    }

    public function getLatestHistory(): ?AdoptionHistory
    {
        if ($this->adoptionHistories->isEmpty()) {
            return null;
        }

        return $this->adoptionHistories->last();
    }
}
