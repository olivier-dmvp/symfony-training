<?php

namespace Application\GraphQL;

use Application\MessageBus\CommandBus;
use Application\Security\Voter\DinosaurVoter;
use Domain\Collection\DinosaursCollection;
use Domain\Exception\DinosaurNotFoundException;
use Domain\Model\Dinosaur;
use Domain\UseCase\CreateDinosaur;
use Domain\UseCase\RemoveDinosaur;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class DinosaurResolver
{
    public function __construct(
        private DinosaursCollection $dinosaurs,
        private CommandBus $commandBus,
        private Security $security,
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
        if (!$this->security->isGranted(DinosaurVoter::CREATE)) {
            throw new AccessDeniedException();
        }

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

    public function removeDinosaur(string $id): string
    {
        if (!$this->security->isGranted(DinosaurVoter::REMOVE)) {
            throw new AccessDeniedException();
        }

        $this->commandBus->dispatch(new RemoveDinosaur\Input($id));

        return $id;
    }
}
