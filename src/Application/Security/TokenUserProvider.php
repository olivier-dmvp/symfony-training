<?php

declare(strict_types=1);

namespace Application\Security;

use Domain\Collection\UsersCollection;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Charge l'utilisateur par l'id porté dans le JWT, pour le firewall graphql.
 */
final class TokenUserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly UsersCollection $users,
    ) {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->users->findOneById((int) $identifier)
            ?? throw new UserNotFoundException();

        return new User($user);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException();
        }

        return $this->loadUserByIdentifier((string) $user->getId());
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
