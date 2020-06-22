<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Test\Config;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Analyzer\Config\ConfigProviderInterface;
use Scheb\Tombstone\Analyzer\Config\Configuration;
use Scheb\Tombstone\Analyzer\Config\ConfigurationLoader;
use Scheb\Tombstone\Analyzer\Test\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationLoaderTest extends TestCase
{
    /**
     * @test
     */
    public function loadConfiguration_multipleProviders_processMergedConfig(): void
    {
        $provider1 = $this->createProviderReturns(['option1' => ['subNode1' => 'subValue1', 'subNode2' => 'subValue2'], 'option2' => 'value2']);
        $provider2 = $this->createProviderReturns(['option1' => ['subNode1' => 'differentSubValue1', 'subNode3' => 'subValue3'], 'option3' => 'value3']);

        $configurationDefinition = $this->createMock(ConfigurationInterface::class);
        $processor = $this->createMock(Processor::class);

        $expectedMergedConfig = [
            Configuration::CONFIG_ROOT => [ // Analyzer root node is added for configuration processing
                'option1' => [
                    'subNode1' => 'differentSubValue1',
                    'subNode2' => 'subValue2',
                    'subNode3' => 'subValue3',
                ],
                'option2' => 'value2',
                'option3' => 'value3',
            ],
        ];

        $processor
            ->expects($this->any())
            ->method('processConfiguration')
            ->with($configurationDefinition, $expectedMergedConfig)
            ->willReturn(['processed']);

        $loader = new ConfigurationLoader($processor, $configurationDefinition);
        $returnValue = $loader->loadConfiguration([$provider1, $provider2]);

        $this->assertEquals(['processed'], $returnValue);
    }

    private function createProviderReturns(array $config): MockObject
    {
        $provider = $this->createMock(ConfigProviderInterface::class);
        $provider
            ->expects($this->any())
            ->method('readConfiguration')
            ->willReturn($config);

        return $provider;
    }
}
