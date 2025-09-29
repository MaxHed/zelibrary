<?php

namespace App\ApiResource;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\Groups;

final class CreateReviewInput
{
    #[Assert\NotBlank]
    #[Groups(['review:write'])]
    public ?string $review = null;

    #[Assert\NotNull]
    #[Assert\Range(min: 1, max: 5)]
    #[Groups(['review:write'])]
    public ?int $rate = null;
}


