<?php

declare(strict_types=1);

namespace Application\Security;

use Domain\Model\User;

interface JWTEncoderInterface
{
    public function encode(User $user): string;

    /**
     * Renvoie l'id de l'utilisateur porté par le token.
     *
     * @throws InvalidTokenException
     */
    public function decode(string $token): int;
}
