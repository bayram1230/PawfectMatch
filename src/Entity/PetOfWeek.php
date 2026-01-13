<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PetOfWeek
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // The highlighted pet
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pet $pet = null;

    // When this pet was selected as pet of the week
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct(Pet $pet)
    {
        $this->pet = $pet;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPet(): ?Pet
    {
        return $this->pet;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
