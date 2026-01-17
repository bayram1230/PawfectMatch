<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:system:safe-shutdown',
    description: 'Checks database and system state before safe shutdown'
)]
class SystemSafeShutdownCommand extends Command
{
    public function __construct(
        private Connection $connection
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('🔐 Running system safe-shutdown checks...');

        try {
            /**
             * 1) Force DB connection + read
             */
            $this->connection->executeQuery('SELECT 1')->fetchOne();
            $output->writeln('✅ Database connection OK');

            /**
             * 2) Force write
             */
            $this->connection->executeStatement(
                'CREATE TEMPORARY TABLE IF NOT EXISTS __healthcheck (id INT)'
            );
            $this->connection->executeStatement(
                'INSERT INTO __healthcheck (id) VALUES (1)'
            );
            $this->connection->executeQuery(
                'SELECT id FROM __healthcheck'
            )->fetchOne();

            $output->writeln('✅ Database read/write OK');

            /**
             * 3) Try MySQL flush (optional, may not be permitted)
             */
            try {
                $this->connection->executeStatement('FLUSH TABLES');
                $this->connection->executeStatement('FLUSH LOGS');
                $output->writeln('✅ MySQL flush executed');
            } catch (\Throwable $flushError) {
                $output->writeln('⚠️ MySQL flush not permitted (continuing safely)');
            }

            /**
             * 4) Small safety pause (IO buffers)
             */
            usleep(500_000); // 0.5s

            $output->writeln('');
            $output->writeln('🟢 SYSTEM READY FOR SHUTDOWN');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln('');
            $output->writeln('❌ SAFE SHUTDOWN CHECK FAILED');
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }
    }
}
