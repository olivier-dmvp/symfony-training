<?php

namespace Application\GraphQL;

use Application\MessageBus\CommandBus;
use Domain\Collection\DinosaursCollection;
use Domain\Exception\DinosaurNotFoundException;
use Domain\Model\Dinosaur;
use Domain\UseCase\CreateDinosaur;

final readonly class DinosaurResolver
{
    public function __construct(
        private DinosaursCollection $dinosaurs,
        private CommandBus $commandBus,
    )
    {
    }

    public function dinosaurs(?string $search): array
    {
        return $this->dinosaurs->search($search);
    }

    public function dinosaur(string $id): Dinosaur
    {
        return $this->dinosaurs->find($id)
            ?? throw new DinosaurNotFoundException($id);
    }

    /**
     * @param array{name: string, gender: string, speciesId: string, age: int, eyesColor: string} $input
     */
    public function createDinosaur(array $input): Dinosaur
    {
        /** @var CreateDinosaur\Output $output */
        $output = $this->commandBus->dispatch(new CreateDinosaur\Input(
            $input['name'],
            $input['gender'],
            $input['speciesId'],
            $input['age'],
            $input['eyesColor'],
        ));

        return $output->dinosaur;
    }
}
