<?php

declare(strict_types=1);

namespace Bingely\Application\UserInterface\Request;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class HomeRequest
{
    public function __construct(
        #[NotBlank]
        #[Length(min: 5, max: 18)]
        public string $name,
        #[NotBlank]
        #[Email]
        public string $email,
    )
    {
    }
}
