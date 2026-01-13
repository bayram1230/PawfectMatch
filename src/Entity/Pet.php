<?php

namespace App\Entity;

use App\Domain\Pets\PetStatus;
use App\Repository\PetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: PetRepository::class)]
#[ORM\Table(name: 'pets')]
class Pet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(length: 20)]
    private string $species;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $age = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $breed = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $sex = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $size = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adoptionRequirements = null;

    #[ORM\ManyToOne]
    private ?User $shelter = null;

    #[ORM\Column(enumType: PetStatus::class)]
    private PetStatus $status;

    #[ORM\OneToMany(
        mappedBy: 'pet',
        targetEntity: AdoptionRequest::class,
        orphanRemoval: true
    )]
    private Collection $adoptionRequests;

    public function __construct()
    {
        $this->adoptionRequests = new ArrayCollection();
        $this->status = PetStatus::ACTIVE;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSpecies(): string
    {
        return $this->species;
    }

    public function setSpecies(string $species): self
    {
        $this->species = $species;
        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;
        return $this;
    }

    public function getImage(): string
    {
        return $this->image ?: 'default-animals.png';
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function setBreed(?string $breed): static
    {
        $this->breed = $breed;
        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(?string $sex): static
    {
        $this->sex = $sex;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): static
    {
        $this->size = $size;
        return $this;
    }

    public function getAdoptionRequirements(): ?string
    {
        return $this->adoptionRequirements;
    }

    public function setAdoptionRequirements(?string $adoptionRequirements): static
    {
        $this->adoptionRequirements = $adoptionRequirements;
        return $this;
    }

    public function getShelter(): ?User
    {
        return $this->shelter;
    }

    public function setShelter(?User $shelter): static
    {
        $this->shelter = $shelter;
        return $this;
    }

    /**
     * @return Collection<int, AdoptionRequest>
     */
    public function getAdoptionRequests(): Collection
    {
        return $this->adoptionRequests;
    }

    public function addAdoptionRequest(AdoptionRequest $request): self
    {
        if (!$this->adoptionRequests->contains($request)) {
            $this->adoptionRequests->add($request);
            $request->setPet($this);
        }

        return $this;
    }

    public function removeAdoptionRequest(AdoptionRequest $request): self
    {
        if ($this->adoptionRequests->removeElement($request)) {
            if ($request->getPet() === $this) {
                $request->setPet(null);
            }
        }

        return $this;
    }

    /**
     * Read-only Status Getter
     */
    public function getStatus(): PetStatus
    {
        return $this->status;
    }

    /**
     * Fachliche Aktion: Pet archivieren
     */
    public function archive(): void
    {
        if ($this->status === PetStatus::ARCHIVED) {
            return;
        }

        $this->status = PetStatus::ARCHIVED;
    }
}
