<?php

namespace Application\GraphQL;

use Domain\Model\Dinosaur;
use Overblog\GraphQLBundle\Resolver\ResolverMap;

final class DinosaurResolverMap extends ResolverMap
{
    protected function map(): array
    {
        return [
            'Dinosaur' => [
                'displayName' => static fn (
                    Dinosaur $dinosaur,
                ): string => sprintf(
                    '%s (%s)',
                    $dinosaur->getName(),
                    $dinosaur->getSpecies()->getName(),
                ),
            ],
        ];
    }
}
