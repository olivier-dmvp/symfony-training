<?php

declare(strict_types=1);

namespace Application\Command;

use Application\Security\InvalidTokenException;
use Application\Security\JWTEncoderInterface;
use Domain\Collection\UsersCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:jwt:decode', description: "Retrouve l'utilisateur porté par un JWT")]
final class DecodeJWTCommand extends Command
{
    public function __construct(
        private readonly JWTEncoderInterface $encoder,
        private readonly UsersCollection $users,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('token', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $id = $this->encoder->decode($input->getArgument('token'));
        } catch (InvalidTokenException $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $user = $this->users->findOneById($id);

        if (null === $user) {
            $io->error(sprintf('Token valide, mais aucun utilisateur avec l\'id %d.', $id));

            return Command::FAILURE;
        }

        $io->definitionList(
            ['id' => $user->getId()],
            ['email' => $user->getEmail()],
            ['roles' => implode(', ', $user->getRoles())],
        );

        return Command::SUCCESS;
    }
}
