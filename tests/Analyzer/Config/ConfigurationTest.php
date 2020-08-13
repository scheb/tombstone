<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Config;

use Scheb\Tombstone\Analyzer\Config\Configuration;
use Scheb\Tombstone\Tests\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    private const ROOT_DIR = __DIR__.'/../../../';
    private const APPLICATION_DIR = self::ROOT_DIR.'/app';
    private const REPORT_DIR = self::APPLICATION_DIR.'/report';
    private const LOGS_DIR = self::APPLICATION_DIR.'/logs';

    private const FULL_CONFIG = [
        'source_code' => [
            'root_directory' => self::APPLICATION_DIR,
        ],
        'tombstones' => [
            'parser' => [
                'excludes' => ['tests'],
                'names' => ['*.php'],
                'not_names' => ['*.js'],
            ],
        ],
        'logs' => [
            'directory' => self::LOGS_DIR,
        ],
        'report' => [
            'php' => self::REPORT_DIR.'/report.php',
            'checkstyle' => self::REPORT_DIR.'/checkstyle.xml',
            'html' => self::REPORT_DIR,
            'console' => true,
        ],
    ];

    private const MINIMUM_CONFIG = [
        'source_code' => [
            'root_directory' => self::APPLICATION_DIR,
        ],
        'logs' => [
            'directory' => self::LOGS_DIR,
        ],
    ];

    private function processConfiguration(array $config): array
    {
        $configuration = new Configuration();
        $processor = new Processor();

        return $processor->processConfiguration($configuration, [Configuration::CONFIG_ROOT => $config]);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_validFullConfig_returnSameConfig(): void
    {
        $config = self::FULL_CONFIG;
        $expectedProcessedConfig = $config;

        $processedConfig = $this->processConfiguration($config);
        $this->assertEquals($expectedProcessedConfig, $processedConfig);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_validMinimumConfig_addDefaultValues(): void
    {
        $config = self::MINIMUM_CONFIG;
        $expectedProcessedConfig = $config;
        $expectedProcessedConfig['tombstones']['parser']['excludes'] = [];
        $expectedProcessedConfig['tombstones']['parser']['names'] = ['*.php'];
        $expectedProcessedConfig['tombstones']['parser']['not_names'] = [];
        $expectedProcessedConfig['report']['console'] = true;
        $expectedProcessedConfig['report']['php'] = null;
        $expectedProcessedConfig['report']['checkstyle'] = null;
        $expectedProcessedConfig['report']['html'] = null;

        $processedConfig = $this->processConfiguration($config);
        $this->assertEquals($expectedProcessedConfig, $processedConfig);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_additionalConfigValue_isPassedThrough(): void
    {
        $config = self::FULL_CONFIG;
        $config['additional'] = 'value';

        $processedConfig = $this->processConfiguration($config);
        $this->assertArrayHasKey('additional', $processedConfig);
        $this->assertEquals('value', $processedConfig['additional']);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_missingSourceCodeNode_throwsException(): void
    {
        $config = self::FULL_CONFIG;
        unset($config['source_code']);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('child node "source_code" at path "" must be configured');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_missingRootDirectory_throwsException(): void
    {
        $config = self::FULL_CONFIG;
        unset($config['source_code']['root_directory']);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('child node "root_directory" at path ".source_code" must be configured');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_emptyRootDirectory_throwsException(): void
    {
        $config = self::FULL_CONFIG;
        $config['source_code']['root_directory'] = '';

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('path ".source_code.root_directory" cannot contain an empty value');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_invalidRootDirectory_throwsException(): void
    {
        $config = self::FULL_CONFIG;
        $config['source_code']['root_directory'] = 'invalid';

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Must be a valid directory path, given: "invalid"');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_missingLogNode_throwsException(): void
    {
        $config = self::FULL_CONFIG;
        unset($config['logs']);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child node "logs" at path "" must be configured.');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_emptyLogDirectory_throwsException(): void
    {
        $config = self::FULL_CONFIG;
        $config['logs']['directory'] = '';

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('path ".logs.directory" cannot contain an empty value');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_invalidLogDirectory_throwsException(): void
    {
        $config = self::FULL_CONFIG;
        $config['logs']['directory'] = 'invalid';

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Must be a valid directory path, given: "invalid"');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_invalidHtmlReportDirectory_throwsException(): void
    {
        $config = self::FULL_CONFIG;
        $config['report']['html'] = 'invalid';

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Must be a writable directory, given: "invalid"');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_invalidPhpReportFile_throwsException(): void
    {
        $config = self::FULL_CONFIG;
        $config['report']['php'] = self::REPORT_DIR;

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Must be a writable file path');
        $this->processConfiguration($config);
    }
}
