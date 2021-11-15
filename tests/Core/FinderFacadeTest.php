<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Core;

use Scheb\Tombstone\Core\FinderFacade;
use Scheb\Tombstone\Tests\TestCase;

class FinderFacadeTest extends TestCase
{
    private $fixtureDir;

    protected function setUp(): void
    {
        $this->fixtureDir = __DIR__.DIRECTORY_SEPARATOR.'FinderFacadeFiles'.DIRECTORY_SEPARATOR;
    }

    public function testFilesCanBeFoundBasedOnConstructorArguments(): void
    {
        $facade = new FinderFacade(
            [$this->fixtureDir, $this->fixtureDir.'bar.phtml'],
            ['bar'],
            ['*.php'],
            ['*Fail.php']
        );

        $this->assertEquals(
            [
                $this->fixtureDir.'bar.phtml',
                $this->fixtureDir.'subdir'.DIRECTORY_SEPARATOR.'bar.php',
            ],
            $facade->findFiles()
        );
    }
}
