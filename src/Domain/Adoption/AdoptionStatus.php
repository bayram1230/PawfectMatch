<?php

namespace App\Domain\Adoption;

final class AdoptionStatus
{
    public const PENDING   = 'PENDING';
    public const APPROVED  = 'APPROVED';
    public const REJECTED  = 'REJECTED';
    public const COMPLETED = 'COMPLETED';
}
