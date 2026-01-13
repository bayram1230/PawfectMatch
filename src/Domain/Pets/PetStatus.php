<?php

namespace App\Domain\Pets;

enum PetStatus: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
}
