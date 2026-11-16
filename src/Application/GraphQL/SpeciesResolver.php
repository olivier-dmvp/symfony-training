<?php

namespace Application\GraphQL;

use Application\MessageBus\CommandBus;
use Domain\Collection\DinosaursCollection;
use Domain\Collection\SpeciesCollection;
use Domain\Exception\DinosaurNotFoundException;
use Domain\Model\Dinosaur;
use Domain\UseCase\CreateDinosaur;

final readonly class SpeciesResolver
{
    public function __construct(
        private SpeciesCollection $species,
    )
    {
    }

    public function species(): array
    {
        return $this->species->findAll();
    }
}
