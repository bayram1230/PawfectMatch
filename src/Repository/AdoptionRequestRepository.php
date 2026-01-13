<?php

namespace App\Repository;

use App\Entity\AdoptionRequest;
use App\Entity\Pet;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdoptionRequest>
 */
final class AdoptionRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdoptionRequest::class);
    }

    /**
     * Find the latest adoption request for a given pet by a specific applicant
     *
     * @return AdoptionRequest|null
     */
    public function findLatestForPetAndApplicant(
        Pet $pet,
        User $applicant
    ): ?AdoptionRequest {
        return $this->createQueryBuilder('r')
            ->andWhere('r.pet = :pet')
            ->andWhere('r.applicant = :applicant')
            ->setParameter('pet', $pet)
            ->setParameter('applicant', $applicant)
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * User dashboard:
     * Count all adoption requests created by the given user
     */
    public function countByApplicant(User $applicant): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.applicant = :applicant')
            ->setParameter('applicant', $applicant)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Shelter / Admin read model:
     * Find all adoption requests for pets owned by a specific shelter
     *
     * @return AdoptionRequest[]
     */
    public function findByShelter(User $shelter): array
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.pet', 'p')
            ->andWhere('p.shelter = :shelter')
            ->setParameter('shelter', $shelter)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
 * @return AdoptionRequest[]
 */
public function findByApplicant(User $applicant): array
{
    return $this->createQueryBuilder('r')
        ->andWhere('r.applicant = :applicant')
        ->setParameter('applicant', $applicant)
        ->orderBy('r.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
}

}
