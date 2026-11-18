<?php

declare(strict_types=1);

namespace Application\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, null>
 */
final class DinosaurVoter extends Voter
{
    public const CREATE = 'DINOSAUR_CREATE';
    public const EDIT = 'DINOSAUR_EDIT';
    public const REMOVE = 'DINOSAUR_REMOVE';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::CREATE, self::EDIT, self::REMOVE], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']);
    }
}
