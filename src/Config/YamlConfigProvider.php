<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Config;

use Scheb\Tombstone\Analyzer\PathTools;
use Symfony\Component\Yaml\Yaml;

class YamlConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string
     */
    private $configFile;

    public function __construct(string $configFile)
    {
        $this->configFile = $configFile;
    }

    public function readConfiguration(): array
    {
        $config = Yaml::parseFile($this->configFile);

        // Make all paths relative to config file path
        $configFileDir = dirname(realpath($this->configFile));

        if (isset($config['rootDir'])) {
            $config['rootDir'] = PathTools::makePathAbsolute($config['rootDir'], $configFileDir);
        }

        if (isset($config['source']['directories'])) {
            $config['source']['directories'] = array_map(function (string $directory) use ($configFileDir): string {
                return PathTools::makePathAbsolute($directory, $configFileDir);
            }, $config['source']['directories']);
        }

        if (isset($config['logs']['directory'])) {
            $config['logs']['directory'] = PathTools::makePathAbsolute($config['logs']['directory'], $configFileDir);
        }

        if (isset($config['logs']['custom']['file'])) {
            $config['logs']['custom']['file'] = PathTools::makePathAbsolute($config['logs']['custom']['file'], $configFileDir);
        }

        if (isset($config['report']['php'])) {
            $config['report']['php'] = PathTools::makePathAbsolute($config['report']['php'], $configFileDir);
        }

        if (isset($config['report']['html'])) {
            $config['report']['html'] = PathTools::makePathAbsolute($config['report']['html'], $configFileDir);
        }

        return $config;
    }
}
