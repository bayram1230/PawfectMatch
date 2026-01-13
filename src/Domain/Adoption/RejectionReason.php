<?php

namespace App\Domain\Adoption;

final class RejectionReason
{
    public const MANUAL_REJECTION = 'manual_rejection';
    public const PET_ALREADY_ADOPTED = 'pet_already_adopted';
    public const REQUEST_WITHDRAWN = 'request_withdrawn';
}
