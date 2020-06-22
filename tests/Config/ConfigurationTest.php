<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Test\Config;

use Scheb\Tombstone\Analyzer\Config\Configuration;
use Scheb\Tombstone\Analyzer\Test\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    private const APPLICATION_DIR = __DIR__.'/../_application';
    private const REPORT_DIR = __DIR__.'/../_report';
    private const LOGS_DIR = __DIR__.'/../_logs';
    private const CUSTOM_LOG_PROVIDER = self::APPLICATION_DIR.'/src/Tombstone/LogProvider.php';

    private const VALID_CONFIG = [
        'source' => [
            'directories' => [
                self::APPLICATION_DIR,
            ],
        ],
        'rootDir' => self::APPLICATION_DIR,
        'logs' => [
            'directory' => self::LOGS_DIR,
            'custom' => [
                'file' => self::CUSTOM_LOG_PROVIDER,
                'class' => 'LogProvider',
            ],
        ],
        'report' => [
            'php' => self::REPORT_DIR.'/report.php',
            'html' => self::REPORT_DIR,
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
    public function getConfigTreeBuilder_additionalConfig_isPassedThrough()
    {
        $config = self::VALID_CONFIG;
        $config['additional'] = 'value';

        $processedConfig = $this->processConfiguration($config);
        $this->assertArrayHasKey('additional', $processedConfig);
        $this->assertEquals('value', $processedConfig['additional']);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_validConfig_returnProcessedConfig()
    {
        $config = self::VALID_CONFIG;
        $expectedProcessedConfig = $config;
        $expectedProcessedConfig['source']['excludes'] = [];
        $expectedProcessedConfig['source']['names'] = ['*.php'];
        $expectedProcessedConfig['source']['notNames'] = [];
        $expectedProcessedConfig['report']['console'] = true;

        $processedConfig = $this->processConfiguration($config);
        $this->assertEquals($expectedProcessedConfig, $processedConfig);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_missingSourceNode_throwsException()
    {
        $config = self::VALID_CONFIG;
        unset($config['source']);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child node "source" at path "root" must be configured.');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_missingSourceDirectory_throwsException()
    {
        $config = self::VALID_CONFIG;
        unset($config['source']['directories']);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('must be configured');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_emptySourceDirectory_throwsException()
    {
        $config = self::VALID_CONFIG;
        $config['source']['directories'] = [];

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('should have at least 1 element(s) defined');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_invalidSourceDirectory_throwsException()
    {
        $config = self::VALID_CONFIG;
        $config['source']['directories'] = ['invalid'];

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Must be a valid directory path, given: "invalid"');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_missingLogNode_throwsException()
    {
        $config = self::VALID_CONFIG;
        unset($config['logs']);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child node "logs" at path "root" must be configured.');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_missingLogProvider_throwsException()
    {
        $config = self::VALID_CONFIG;
        $config['logs'] = [];

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Must have at least one log provider configured');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_emptyLogDirectory_throwsException()
    {
        $config = self::VALID_CONFIG;
        $config['logs']['directory'] = null;

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('cannot contain an empty value, but got null');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_customLogProvider_throwsException()
    {
        $config = self::VALID_CONFIG;
        unset($config['logs']['directory']);
        unset($config['logs']['custom']['file']); // Not set
        $config['logs']['custom']['class'] = 'LogsProvider';

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child node "file" at path "root.logs.custom" must be configured');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_customLogProviderInvalidFile_throwsException()
    {
        $config = self::VALID_CONFIG;
        unset($config['logs']['directory']);
        $config['logs']['custom']['file'] = 'invalid'; // Directory cannot be a valid file
        $config['logs']['custom']['class'] = 'LogsProvider';

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Must be a valid file path, given: "invalid"');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_invalidLogDirectory_throwsException()
    {
        $config = self::VALID_CONFIG;
        $config['logs']['directory'] = 'invalid';

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Must be a valid directory path, given: "invalid"');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_invalidRootDirectory_throwsException()
    {
        $config = self::VALID_CONFIG;
        $config['rootDir'] = 'invalid';

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Must be a valid directory path, given: "invalid"');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_invalidHtmlReportDirectory_throwsException()
    {
        $config = self::VALID_CONFIG;
        $config['report']['html'] = 'invalid';

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Must be a writable directory, given: "invalid"');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_invalidPhpReportFile_throwsException()
    {
        $config = self::VALID_CONFIG;
        $config['report']['php'] = self::REPORT_DIR;

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Must be a writable file path');
        $this->processConfiguration($config);
    }
}
