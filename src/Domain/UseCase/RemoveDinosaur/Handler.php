<?php

declare(strict_types=1);

namespace Domain\UseCase\RemoveDinosaur;

use Domain\Collection\DinosaursCollection;
use Domain\Event\DinosaurDied;
use Domain\Exception\DinosaurNotFoundException;
use Domain\EventsRegisterer;

final class Handler
{
    public function __construct(
        private readonly DinosaursCollection $dinosaursCollection,
        private readonly EventsRegisterer $eventsRegisterer
    ) {
    }

    public function __invoke(Input $input): Output
    {
        $dinosaur = $this
            ->dinosaursCollection
            ->find($input->id);

        if (null === $dinosaur) {
            throw new DinosaurNotFoundException($input->id);
        }

        // Doctrine désinitialise l'id au flush de la suppression.
        $event = new DinosaurDied($dinosaur->getId(), $dinosaur->getName());

        $this->dinosaursCollection->remove($dinosaur);

        $this->eventsRegisterer->register($event);

        return new Output();
    }
}
