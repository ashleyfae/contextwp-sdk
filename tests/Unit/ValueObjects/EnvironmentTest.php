<?php
/**
 * EnvironmentTest.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace ContextWP\Tests\Unit\ValueObjects;

use ContextWP\SDK;
use ContextWP\Tests\TestCase;
use ContextWP\ValueObjects\Environment;
use ReflectionException;
use WP_Mock;

class EnvironmentTest extends TestCase
{
    /**
     * @covers \ContextWP\ValueObjects\Environment::toArray()
     */
    public function testCanToArray(): void
    {
        $environment = $this->createPartialMock(
            Environment::class,
            ['getSourceHash', 'getBlogValue', 'getEnvironmentType']
        );

        $environment->expects($this->once())
            ->method('getSourceHash')
            ->willReturn('hash');

        $environment->expects($this->exactly(2))
            ->method('getBlogValue')
            ->withConsecutive(['version'], ['language'])
            ->willReturnOnConsecutiveCalls('5.6', 'en_GB');

        $environment->expects($this->once())
            ->method('getEnvironmentType')
            ->willReturn('local');

        $this->mockStaticMethod(SDK::class, 'getVersion')
            ->once()
            ->andReturn('1.1');

        WP_Mock::userFunction('is_multisite')
            ->once()
            ->andReturn(false);

        $expected = [
            'source_hash' => 'hash',
            'wp_version'  => '5.6',
            'php_version' => phpversion(),
            'locale'      => 'en_GB',
            'type'        => 'local',
            'multisite'   => false,
            'sdk_version' => '1.1',
        ];

        $actual = $environment->toArray();

        ksort($expected);
        ksort($actual);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers       \ContextWP\ValueObjects\Environment::getBlogValue()
     * @dataProvider providerCanGetBlogValue
     * @throws ReflectionException
     */
    public function testCanGetBlogValue($result, ?string $expected)
    {
        WP_Mock::userFunction('get_bloginfo')
            ->with('language')
            ->andReturn($result);

        $this->assertSame(
            $expected,
            $this->invokeInaccessibleMethod(new Environment(), 'getBlogValue', 'language')
        );
    }

    /** @see testCanGetBlogValue */
    public function providerCanGetBlogValue(): \Generator
    {
        yield 'false return value' => [false, null];
        yield 'empty string return value' => ['', null];
        yield 'actual value' => ['en_GB', 'en_GB'];
    }
}
