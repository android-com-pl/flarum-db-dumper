<?php

namespace ACPL\FlarumDbDumper;

use Carbon\Carbon;
use Exception;
use Flarum\Console\AbstractCommand;
use Flarum\Foundation\{Config, Paths};
use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Compressors\{GzipCompressor, Bzip2Compressor};
use Spatie\DbDumper\Exceptions\CannotSetParameter;
use Symfony\Component\Console\Input\{InputArgument, InputOption};

class DumbDbCommand extends AbstractCommand
{
    private const COMPRESSORS = [
        'gz' => GzipCompressor::class,
        'bz2' => Bzip2Compressor::class,
    ];

    public function __construct(protected Config $config, protected Paths $paths)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('db:dump')
            ->setDescription('Dump the contents of a database')
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'Path where to store the dump file',
            )
            ->addOption(
                'compress',
                null,
                InputOption::VALUE_REQUIRED,
                'Compression type (gz, bz2)',
            )
            ->addOption(
                'binary-path',
                null,
                InputOption::VALUE_REQUIRED,
                'Custom location for the mysqldump binary',
            )
            ->addOption(
                'include-tables',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma separated list of tables to include in the dump',
            )
            ->addOption(
                'exclude-tables',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma separated list of tables to exclude from the dump',
            )
            ->addOption(
                'skip-structure',
                null,
                InputOption::VALUE_NONE,
                'Skip table structure (CREATE TABLE statements)',
            )
            ->addOption(
                'no-data',
                null,
                InputOption::VALUE_NONE,
                'Do not write row data',
            )
            ->addOption(
                'skip-auto-increment',
                null,
                InputOption::VALUE_NONE,
                'Skip AUTO_INCREMENT values from the dump',
            )
            ->addOption(
                'no-column-statistics',
                null,
                InputOption::VALUE_NONE,
                'Do not use column statistics (for MySQL 8 compatibility with older versions)',
            );
    }

    /**
     * @throws CannotSetParameter
     */
    protected function fire(): int
    {
        $dbConfig = $this->config['database'];
        $dumper = MySql::create()
            ->setHost($dbConfig['host'])
            ->setDbName($dbConfig['database'])
            ->setPort($dbConfig['port'] ?? 3306)
            ->setUserName($dbConfig['username'])
            ->setPassword($dbConfig['password']);

        $path = $this->input->getArgument('path');
        if (empty($path)) {
            $path = $this->paths->storage.'/dumps/dump-'.Carbon::now()->format('Y-m-d-His').'.sql';
        }

        $compression = $this->input->getOption('compress');
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        // If compression is specified and different from path extension
        if ($compression && $extension !== $compression) {
            $path .= '.'.$compression;
        } elseif (! $extension) {
            $path .= '.sql';
        }

        $finalExtension = pathinfo($path, PATHINFO_EXTENSION);
        if (isset(self::COMPRESSORS[$finalExtension])) {
            $compressorClass = self::COMPRESSORS[$finalExtension];
            $dumper->useCompressor(new $compressorClass());
        }

        $dir = dirname($path);
        if (! file_exists($dir)) {
            mkdir($dir, 0755, true);
        }


        if ($binaryPath = $this->input->getOption('binary-path')) {
            $dumper->setDumpBinaryPath($binaryPath);
        }

        if ($includeTables = $this->input->getOption('include-tables')) {
            $dumper->includeTables(explode(',', $includeTables));
        }

        if ($excludeTables = $this->input->getOption('exclude-tables')) {
            $dumper->excludeTables(explode(',', $excludeTables));
        }

        if ($this->input->getOption('skip-structure')) {
            $dumper->doNotCreateTables();
        }

        if ($this->input->getOption('no-data')) {
            $dumper->doNotDumpData();
        }

        if ($this->input->getOption('skip-auto-increment')) {
            $dumper->skipAutoIncrement();
        }

        if ($this->input->getOption('no-column-statistics')) {
            $dumper->doNotUseColumnStatistics();
        }

        try {
            $dumper->dumpToFile($path);
            $this->info("Database dumped successfully to: $path");
        } catch (Exception $e) {
            $this->error('Failed to dump database: '.$e->getMessage());
            return 1;
        }

        return 0;
    }
}
