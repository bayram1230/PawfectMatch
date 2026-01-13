<?php

namespace App\Repository;

use App\Entity\Pet;
use App\Domain\Pets\PetStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pet>
 */
class PetRepository extends ServiceEntityRepository
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
     * All pets (default public listing)
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
     * Fostered pets
     * A pet is considered fostered if its latest adoption history status is "Approved".
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
     * A pet is considered adopted if its latest adoption history status is "Completed".
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
     * Current pet of the week
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
     * Find all available pets for matchmaker scoring
     *
     * A pet is considered available if:
     * - it is ACTIVE
     * - it has no adoption request yet
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
