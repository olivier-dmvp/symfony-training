<?php

declare(strict_types=1);

namespace Application\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class BearerAuthenticator extends AbstractAuthenticator
{
    private const PREFIX = 'Bearer ';

    public function __construct(
        private readonly JWTEncoderInterface $encoder,
    ) {
    }

    // Sans en-tête Bearer, la requête reste anonyme : /graphql reste publique.
    public function supports(Request $request): ?bool
    {
        return str_starts_with((string) $request->headers->get('Authorization'), self::PREFIX);
    }

    public function authenticate(Request $request): Passport
    {
        $token = substr((string) $request->headers->get('Authorization'), \strlen(self::PREFIX));

        try {
            $id = $this->encoder->decode($token);
        } catch (InvalidTokenException $exception) {
            throw new CustomUserMessageAuthenticationException($exception->getMessage(), previous: $exception);
        }

        // Le provider du firewall (TokenUserProvider) charge l'utilisateur à partir de l'id.
        return new SelfValidatingPassport(new UserBadge((string) $id));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            ['message' => strtr($exception->getMessageKey(), $exception->getMessageData())],
            Response::HTTP_UNAUTHORIZED,
        );
    }
}
