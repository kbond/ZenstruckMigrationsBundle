<?php

namespace Zenstruck\Bundle\MigrationsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper;
use Doctrine\Bundle\MigrationsBundle\Command\DoctrineCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand as BaseMigrateCommand;
use Zenstruck\Bundle\MigrationsBundle\Migrations\AbstractMigration;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class MigrateCommand extends BaseMigrateCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('zenstruck:migrations:migrate')
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command.')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineCommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('em'));
        $configuration = $this->getMigrationConfiguration($input, $output);
        DoctrineCommand::configureMigrations($this->getApplication()->getKernel()->getContainer(), $configuration);

        $from = $configuration->getCurrentVersion();
        $to = $input->getArgument('version');

        if ($to === null) {
            $to = $configuration->getLatestVersion();
        }

        $direction = $from > $to ? 'down' : 'up';
        $migrationsToExecute = $configuration->getMigrationsToExecute($direction, $to);

        parent::execute($input, $output);

        foreach ($migrationsToExecute as $version) {
            $migration = $version->getMigration();
            if ($migration instanceof AbstractMigration) {
                $output->writeln('');
                $output->writeln(sprintf('Running data migration %s to: <info>%s</info>', $direction, $migration->getDataDescription()));
                if ('up' == $direction) {
                    $migration->dataUp($this->getApplication()->getKernel()->getContainer());
                } else {
                    $migration->dataDown($this->getApplication()->getKernel()->getContainer());
                }
            }
        }
    }
}
