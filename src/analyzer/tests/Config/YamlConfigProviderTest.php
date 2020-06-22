<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Test\Config;

use Scheb\Tombstone\Analyzer\Config\YamlConfigProvider;
use Scheb\Tombstone\Analyzer\Test\TestCase;

class YamlConfigProviderTest extends TestCase
{
    private const CONFIG_DIR = __DIR__.DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR;

    protected function readConfiguration(string $file): array
    {
        $configProvider = new YamlConfigProvider(self::CONFIG_DIR.$file);

        return $configProvider->readConfiguration();
    }

    /**
     * @test
     */
    public function processConfiguration_minimum_haveDirectoriesSet(): void
    {
        $config = $this->readConfiguration('minimum.yml');

        $expectedConfig = [
            'source' => [
                'directories' => [
                    self::CONFIG_DIR.'src',
                ],
            ],
            'logs' => [
                'directory' => self::CONFIG_DIR.'logs',
            ],
        ];

        $this->assertEquals($expectedConfig, $config);
    }

    /**
     * @test
     */
    public function processConfiguration_fullConfig_haveAllValuesSet(): void
    {
        $config = $this->readConfiguration('full.yml');

        $expectedConfig = [
            'source' => [
                'directories' => [
                    self::CONFIG_DIR.'src',
                ],
                'excludes' => [
                    'tests',
                ],
                'names' => [
                    '*.php',
                ],
                'notNames' => [
                    '*.js',
                ],
            ],
            'rootDir' => self::CONFIG_DIR.'root',
            'logs' => [
                'directory' => self::CONFIG_DIR.'logs',
                'custom' => [
                    'file' => self::CONFIG_DIR.'src'.DIRECTORY_SEPARATOR.'Tombstone'.DIRECTORY_SEPARATOR.'LogProvider.php',
                    'class' => 'Scheb\Tombstone\Analyzer\TestApplication\Tombstone\LogProvider',
                ],
            ],
            'report' => [
                'php' => self::CONFIG_DIR.'report'.DIRECTORY_SEPARATOR.'tombstone-report.php',
                'html' => self::CONFIG_DIR.'report',
                'console' => true,
            ],
        ];

        $this->assertEquals($expectedConfig, $config);
    }
}
