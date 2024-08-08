<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:doctrine-setup',
    description: 'Runs a series of Doctrine commands to reset the database and MongoDB schema'
)]
class DatabasesInitialisation extends Command
{
    protected static $defaultName = 'app:doctrine-setup';
    protected static $defaultDescription = 'Runs a series of Doctrine commands to reset the database and MongoDB schema';

    protected function configure(): void
    {
        // Optionally, you can add descriptions for your command and options.
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $commands = [
            ['php', 'bin/console', 'doctrine:database:drop', '--force'],
            ['php', 'bin/console', 'doctrine:database:create'],
            ['php', 'bin/console', 'doctrine:migrations:migrate', '--no-interaction'],
            ['php', 'bin/console', 'doctrine:fixtures:load', '--no-interaction'],
            ['php', 'bin/console', 'doctrine:mongodb:schema:drop'],
            ['php', 'bin/console', 'doctrine:mongodb:schema:create'],
        ];

        foreach ($commands as $command) {
            $process = new Process($command);
            $process->setTimeout(null);

            try {
                $process->mustRun();

                $io->success(sprintf('Command "%s" ran successfully.', implode(' ', $command)));
            } catch (ProcessFailedException $exception) {
                $io->error(sprintf('Command "%s" failed with error: %s', implode(' ', $command), $exception->getMessage()));

                return Command::FAILURE;
            }
        }

        $io->success('All Doctrine commands executed successfully.');

        return Command::SUCCESS;
    }
}