<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Config;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationLoader
{
    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(Processor $processor, ConfigurationInterface $configuration)
    {
        $this->processor = $processor;
        $this->configuration = $configuration;
    }

    public static function create(): self
    {
        return new self(new Processor(), new Configuration());
    }

    /**
     * @param ConfigProviderInterface[] $configProviders
     */
    public function loadConfiguration(array $configProviders): array
    {
        $rawConfig = [];
        foreach ($configProviders as $configProvider) {
            $rawConfig = array_replace_recursive($rawConfig, $configProvider->readConfiguration());
        }

        return $this->processor->processConfiguration($this->configuration, [Configuration::CONFIG_ROOT => $rawConfig]);
    }
}
