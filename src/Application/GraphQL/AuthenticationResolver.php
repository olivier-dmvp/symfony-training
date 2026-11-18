<?php

declare(strict_types=1);

namespace Application\GraphQL;

use Application\Security\JWTEncoderInterface;
use Application\Security\User as SecurityUser;
use Domain\Collection\UsersCollection;
use Overblog\GraphQLBundle\Error\UserError;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class AuthenticationResolver
{
    public function __construct(
        private UsersCollection $users,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTEncoderInterface $encoder,
    ) {
    }

    public function login(string $email, string $password): string
    {
        $user = $this->users->findOneByEmail($email);

        if (null === $user || !$this->passwordHasher->isPasswordValid(new SecurityUser($user), $password)) {
            throw new UserError('Identifiants invalides.');
        }

        return $this->encoder->encode($user);
    }
}
