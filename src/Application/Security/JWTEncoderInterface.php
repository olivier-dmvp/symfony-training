<?php

declare(strict_types=1);

namespace Application\Security;

interface JWTEncoderInterface
{
    public function encode(User $user): string;

    /**
     * @throws InvalidTokenException
     */
    public function decode(string $token): User;
}
