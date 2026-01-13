<?php

namespace App\Repository;

use App\Entity\AdoptionRequest;
use App\Entity\Pet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdoptionRequest>
 */
class AdoptionRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdoptionRequest::class);
    }

    public function findLatestForPetAndUsername(Pet $pet, string $username): ?AdoptionRequest
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.pet = :pet')
            ->andWhere('r.username = :username')
            ->setParameter('pet', $pet)
            ->setParameter('username', $username)
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
