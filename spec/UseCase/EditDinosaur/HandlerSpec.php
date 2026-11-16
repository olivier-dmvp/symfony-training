<?php

namespace spec\Domain\UseCase\EditDinosaur;

use Domain\Collection\DinosaursCollection;
use Domain\Collection\SpeciesCollection;
use Domain\Model\Dinosaur;
use Domain\Model\Species;
use Domain\UseCase\EditDinosaur\Handler;
use Domain\UseCase\EditDinosaur\Input;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;

class HandlerSpec extends ObjectBehavior
{
    function let(
        DinosaursCollection $dinosaurs,
        SpeciesCollection $species,
    ): void {
        $this->beConstructedWith($dinosaurs, $species);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(Handler::class);
    }

    function it_changes_the_birthdate_of_the_dinosaur(
        DinosaursCollection $dinosaurs,
        SpeciesCollection $species,
    ): void {
        $tyrannosaurus = new Species('Tyrannosaurus', ['forest'], 'carnivore');
        $dinosaur = new Dinosaur(
            'Rex',
            'male',
            $tyrannosaurus,
            12,
            '#ff0000',
            new \DateTimeImmutable('2021-01-01'),
        );

        $dinosaurs->find('42')->willReturn($dinosaur);
        $species->find('1')->willReturn($tyrannosaurus);

        $this(new Input('42', 'Rex', 'male', '1', 12, '#ff0000', new \DateTimeImmutable('2015-06-15')));

        if ($dinosaur->getBirthdate() != new \DateTimeImmutable('2015-06-15')) {
            throw new FailureException('The birthdate was not changed');
        }
    }
}
