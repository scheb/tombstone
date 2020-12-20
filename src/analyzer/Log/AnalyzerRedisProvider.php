<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Log;

use Redis;
use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Core\Format\AnalyzerLogFormat;
use Scheb\Tombstone\Core\Model\RootPath;

class AnalyzerRedisProvider implements LogProviderInterface
{
    /**
     * @var RootPath
     */
    private $rootDir;

    /**
     * @var Redis
     */
    private $redis;

    /**
     * @var ConsoleOutputInterface
     */
    private $output;

    public function __construct(RootPath $rootDir, Redis $redis, ConsoleOutputInterface $output)
    {
        $this->rootDir = $rootDir;
        $this->redis = $redis;
        $this->output = $output;
    }

    public static function create(array $config, ConsoleOutputInterface $consoleOutput): LogProviderInterface
    {
        $rootDir = new RootPath($config['source_code']['root_directory']);

        $redis = new Redis();
        if (isset($config['driver']['password'])) {
            $redis->auth($config['driver']['password']);
        }

        $redis->connect(
            $config['driver']['host'] ?: '',
            $config['driver']['port'] ?: 6379,
            $config['driver']['timeout'] ?: 0.0,
            $config['driver']['reserved'] ?: null,
            $config['driver']['retryInterval'] ?: 0,
            $config['driver']['readTimeout'] ?: 0.0
        );

        return new self(
            $rootDir,
            $redis,
            $consoleOutput
        );
    }

    public function getVampires(): iterable
    {
        $files = $this->redis->keys('*.tombstone');

        $this->output->writeln('Read analyzer log data ...');
        $progress = $this->output->createProgressBar(\count($files));

        foreach ($files as $file) {
            // This is done to reset keys
            foreach ($this->redis->xRead([$file => '0-0']) as $rows) {
                foreach ($rows as $timestamp => $line) {
                    yield AnalyzerLogFormat::logToVampire($line, $this->rootDir);
                }
            }
            $progress->advance();
        }
        $this->output->writeln();
    }
}
