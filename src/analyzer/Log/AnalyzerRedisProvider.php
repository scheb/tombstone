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
            isset($config['driver']['host']) ? $config['driver']['host'] : '',
            isset($config['driver']['port']) ? $config['driver']['port'] : 6379,
            isset($config['driver']['timeout']) ? $config['driver']['timeout'] : 0.0,
            isset($config['driver']['reserved']) ? $config['driver']['reserved'] : null,
            isset($config['driver']['retryInterval']) ? $config['driver']['retryInterval'] : 0,
            isset($config['driver']['readTimeout']) ? $config['driver']['readTimeout'] : 0.0
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
                    yield AnalyzerLogFormat::logToVampire($line['data'], $this->rootDir);
                }
            }
            $progress->advance();
        }
        $this->output->writeln();
    }
}
