<?php

declare(strict_types=1);

namespace Application\Command;

use Application\Security\JWTEncoderInterface;
use Application\Security\User;
use Domain\Collection\UsersCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:jwt:encode', description: 'Génère un JWT pour un utilisateur')]
final class EncodeJWTCommand extends Command
{
    public function __construct(
        private readonly UsersCollection $users,
        private readonly JWTEncoderInterface $encoder,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        $user = $this->users->findOneByEmail($email);

        if (null === $user) {
            $io->error(sprintf('Aucun utilisateur "%s".', $email));

            return Command::FAILURE;
        }

        $io->writeln($this->encoder->encode(new User($user)));

        return Command::SUCCESS;
    }
}
