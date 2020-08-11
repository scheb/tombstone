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

    private const VALID_CONFIG = [
        'source' => [
            'rootDirectory' => self::APPLICATION_DIR,
        ],
        'rootDir' => self::APPLICATION_DIR,
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
    public function getConfigTreeBuilder_missingRootDirectory_throwsException()
    {
        $config = self::VALID_CONFIG;
        unset($config['source']['rootDirectory']);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('must be configured');
        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function getConfigTreeBuilder_invalidRootDirectory_throwsException()
    {
        $config = self::VALID_CONFIG;
        $config['source']['rootDirectory'] = 'invalid';

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
