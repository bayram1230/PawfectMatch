<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create a user account',
)]
final class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        // Username
        $username = $helper->ask(
            $input,
            $output,
            new Question('Username: ')
        );

        if (!$username) {
            $output->writeln('<error>Username is required</error>');
            return Command::FAILURE;
        }

        // Email
        $email = $helper->ask(
            $input,
            $output,
            new Question('Email: ')
        );

        if (!$email) {
            $output->writeln('<error>Email is required</error>');
            return Command::FAILURE;
        }

        // Password
        $passwordQuestion = new Question('Password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);

        $plainPassword = $helper->ask($input, $output, $passwordQuestion);

        if (!$plainPassword) {
            $output->writeln('<error>Password is required</error>');
            return Command::FAILURE;
        }

        // Role
        $role = $helper->ask(
            $input,
            $output,
            new ChoiceQuestion(
                'Role:',
                ['ROLE_ADMIN', 'ROLE_SHELTER', 'ROLE_USER']
            )
        );

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles([$role]);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $plainPassword)
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('<info>User created successfully</info>');

        return Command::SUCCESS;
    }
}
