<?php

declare(strict_types=1);

namespace Application\Security\Middleware;

use Application\Security\Voter\DinosaurVoter;
use Domain\UseCase\CreateDinosaur;
use Domain\UseCase\CreateSpecies;
use Domain\UseCase\EditDinosaur;
use Domain\UseCase\EditSpecies;
use Domain\UseCase\RegisterUser;
use Domain\UseCase\RemoveDinosaur;
use Domain\UseCase\RemoveSpecies;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class AuthorizationMiddleware implements MiddlewareInterface
{
    private const USE_CASE_NAMESPACE = 'Domain\\UseCase\\';

    private const ATTRIBUTES = [
        CreateDinosaur\Input::class => DinosaurVoter::CREATE,
        EditDinosaur\Input::class => DinosaurVoter::EDIT,
        RemoveDinosaur\Input::class => DinosaurVoter::REMOVE,
        CreateSpecies\Input::class => 'ROLE_ADMIN',
        EditSpecies\Input::class => 'ROLE_ADMIN',
        RemoveSpecies\Input::class => 'ROLE_ADMIN',
        RegisterUser\Input::class => 'PUBLIC_ACCESS',
    ];

    public function __construct(
        private Security $security,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $messageClass = $envelope->getMessage()::class;

        // Les messages internes de Symfony (mailer, notifier...) passent aussi par ce bus.
        if (!str_starts_with($messageClass, self::USE_CASE_NAMESPACE)) {
            return $stack->next()->handle($envelope, $stack);
        }

        $attribute = self::ATTRIBUTES[$messageClass] ?? null;

        if (null === $attribute || !$this->security->isGranted($attribute)) {
            throw new AccessDeniedException();
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
