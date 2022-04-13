<?php
/**
 * CliProviderTest.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Tests\Unit\Cli;

use ContextWP\Cli\CliProvider;
use ContextWP\Tests\TestCase;
use Generator;

class CliProviderTest extends TestCase
{
    /**
     * @covers       \ContextWP\Cli\CliProvider::load()
     * @dataProvider providerCanLoad
     */
    public function testCanLoad(bool $shouldLoad): void
    {
        $provider = $this->createPartialMock(CliProvider::class, ['shouldLoad', 'registerCommands']);

        $provider->expects($this->once())
            ->method('shouldLoad')
            ->willReturn($shouldLoad);

        $provider->expects($shouldLoad ? $this->once() : $this->never())
            ->method('registerCommands')
            ->willReturn(null);

        $provider->load();

        $this->assertConditionsMet();
    }

    /** @see testCanLoad */
    public function providerCanLoad(): Generator
    {
        yield 'should load' => [true];
        yield 'do not load' => [false];
    }
}
