<?php

namespace App\Service\Adoption;

interface AdoptionEntityManager
{
    public function persist(object $entity): void;
    public function flush(): void;
}
