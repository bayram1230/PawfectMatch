<?php

namespace App\Repository;

use App\Entity\Pet;
use App\Entity\User;
use App\Domain\Pets\PetStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pet>
 */
final class PetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pet::class);
    }

    /**
     * Pets for the homepage (latest first, limited)
     *
     * @return Pet[]
     */
    public function findForHomepage(int $limit = 12): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PetStatus::ACTIVE)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Public pet listing (ACTIVE only)
     *
     * @return Pet[]
     */
    public function findAllPets(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PetStatus::ACTIVE)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Admin dashboard:
     * All ACTIVE pets across all shelters
     *
     * @return Pet[]
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PetStatus::ACTIVE)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Shelter dashboard:
     * ACTIVE pets belonging to the given shelter
     *
     * @return Pet[]
     */
    public function findActiveByShelter(User $shelter): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->andWhere('p.shelter = :shelter')
            ->setParameter('status', PetStatus::ACTIVE)
            ->setParameter('shelter', $shelter)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Fostered pets
     *
     * A pet is considered fostered if its LATEST adoption history
     * entry has status "Approved".
     *
     * @return Pet[]
     */
    public function findFosteredPets(): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin(
                'App\Entity\AdoptionRequest',
                'ar',
                'WITH',
                'ar.pet = p'
            )
            ->innerJoin(
                'App\Entity\AdoptionHistory',
                'ah',
                'WITH',
                'ah.adoptionRequest = ar'
            )
            ->andWhere('ah.status = :status')
            ->andWhere('ah.id = (
                SELECT MAX(ah2.id)
                FROM App\Entity\AdoptionHistory ah2
                WHERE ah2.adoptionRequest = ar
            )')
            ->setParameter('status', 'Approved')
            ->groupBy('p.id')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Adopted pets
     *
     * A pet is considered adopted if its LATEST adoption history
     * entry has status "Completed".
     *
     * @return Pet[]
     */
    public function findAdoptedPets(): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin(
                'App\Entity\AdoptionRequest',
                'ar',
                'WITH',
                'ar.pet = p'
            )
            ->innerJoin(
                'App\Entity\AdoptionHistory',
                'ah',
                'WITH',
                'ah.adoptionRequest = ar'
            )
            ->andWhere('ah.status = :status')
            ->andWhere('ah.id = (
                SELECT MAX(ah2.id)
                FROM App\Entity\AdoptionHistory ah2
                WHERE ah2.adoptionRequest = ar
            )')
            ->setParameter('status', 'Completed')
            ->groupBy('p.id')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Current Pet of the Week
     *
     * @return Pet|null
     */
    public function findPetOfWeek(): ?Pet
    {
        return $this->createQueryBuilder('p')
            ->innerJoin(
                'App\Entity\PetOfWeek',
                'pow',
                'WITH',
                'pow.pet = p'
            )
            ->andWhere('p.status = :status')
            ->setParameter('status', PetStatus::ACTIVE)
            ->orderBy('pow.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Matchmaker candidates
     *
     * A pet is considered available if:
     * - it is ACTIVE
     * - it has NO adoption requests yet
     *
     * @return Pet[]
     */
    public function findMatchCandidates(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PetStatus::ACTIVE)
            ->leftJoin(
                'App\Entity\AdoptionRequest',
                'ar',
                'WITH',
                'ar.pet = p'
            )
            ->andWhere('ar.id IS NULL')
            ->getQuery()
            ->getResult();
    }
}
