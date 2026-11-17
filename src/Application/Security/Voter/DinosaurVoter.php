<?php

declare(strict_types=1);

namespace Application\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, null>
 */
final class DinosaurVoter extends Voter
{
    public const CREATE = 'DINOSAUR_CREATE';

    public function __construct(
        private readonly Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::CREATE === $attribute;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // isGranted() plutôt que getRoles() pour respecter role_hierarchy.
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
