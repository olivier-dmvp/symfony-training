<?php

declare(strict_types=1);

namespace Infrastructure\Lexik;

use Application\Security\InvalidTokenException;
use Application\Security\JWTEncoderInterface;
use Application\Security\User as SecurityUser;
use Domain\Collection\UsersCollection;
use Domain\Model\User;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

final class JWTEncoder implements JWTEncoderInterface
{
    public function __construct(
        private readonly JWTTokenManagerInterface $tokenManager,
        private readonly UsersCollection $users,
    ) {
    }

    public function encode(User $user): string
    {
        return $this->tokenManager->create(new SecurityUser($user));
    }

    public function decode(string $token): User
    {
        try {
            $payload = $this->tokenManager->parse($token);
        } catch (JWTDecodeFailureException $exception) {
            throw new InvalidTokenException($exception->getMessage(), previous: $exception);
        }

        $id = $payload[$this->tokenManager->getUserIdClaim()]
            ?? throw new InvalidTokenException('Identifiant absent du token.');

        return $this->users->findOneById((int) $id)
            ?? throw new InvalidTokenException('Utilisateur du token introuvable.');
    }
}
