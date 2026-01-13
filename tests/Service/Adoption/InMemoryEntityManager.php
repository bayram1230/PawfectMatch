<?php

namespace App\Tests\Service\Adoption;

use App\Service\Adoption\AdoptionEntityManager;

class InMemoryEntityManager implements AdoptionEntityManager
{
    private array $persisted = [];

    public function persist(object $entity): void
    {
        $this->persisted[] = $entity;
    }

    public function flush(): void
    {
        // no-op
    }

    public function getPersisted(string $class): array
    {
        return array_values(array_filter(
            $this->persisted,
            fn ($e) => $e instanceof $class
        ));
    }
}
