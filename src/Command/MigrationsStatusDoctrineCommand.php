<?php

declare(strict_types=1);

namespace Gheb\DataMigrationsBundle\Command;

use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to view the status of a set of migrations.
 */
class MigrationsStatusDoctrineCommand extends StatusCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('gheb:data-migrations:status')
            ->addOption('db', null, InputOption::VALUE_REQUIRED, 'The database connection to use for this command.')
            ->addOption('em', null, InputOption::VALUE_REQUIRED, 'The entity manager to use for this command.')
            ->addOption('shard', null, InputOption::VALUE_REQUIRED, 'The shard connection to use for this command.');
    }

    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        /** @var Application $application */
        $application = $this->getApplication();

        Helper\DoctrineCommandHelper::setApplicationHelper($application, $input);

        $configuration = $this->getMigrationConfiguration($input, $output);
        DoctrineCommand::configureMigrations($application->getKernel()->getContainer(), $configuration);

        parent::initialize($input, $output);
    }

    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        // EM and DB options cannot be set at same time
        if (null !== $input->getOption('em') && null !== $input->getOption('db')) {
            throw new InvalidArgumentException('Cannot set both "em" and "db" for command execution.');
        }

        return parent::execute($input, $output);
    }
}
