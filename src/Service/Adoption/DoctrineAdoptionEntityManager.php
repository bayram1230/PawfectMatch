<?php

namespace App\Service\Adoption;

use Doctrine\ORM\EntityManagerInterface;

class DoctrineAdoptionEntityManager implements AdoptionEntityManager
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function persist(object $entity): void
    {
        $this->entityManager->persist($entity);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
