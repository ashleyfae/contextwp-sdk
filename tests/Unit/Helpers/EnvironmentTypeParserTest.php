<?php
/**
 * EnvironmentTypeParserTest.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace ContextWP\Tests\Unit\Helpers;

use ContextWP\Helpers\EnvironmentTypeParser;
use ContextWP\Tests\TestCase;
use Generator;

class EnvironmentTypeParserTest extends TestCase
{
    /**
     * @covers \ContextWP\Helpers\EnvironmentTypeParser::parse()
     * @dataProvider providerCanParse
     */
    public function testCanParse(?string $wpType, bool $shouldGuessFromDomain, string $expected)
    {
        $parser = $this->createPartialMock(EnvironmentTypeParser::class, ['getWpDefinedType', 'guessTypeFromDomain']);
        $parser->expects($this->once())
            ->method('getWpDefinedType')
            ->willReturn($wpType);

        $parser->expects($shouldGuessFromDomain ? $this->once() : $this->never())
            ->method('guessTypeFromDomain')
            ->willReturn('production');

        $this->assertSame($expected, $parser->parse());
    }

    /** @see testCanParse */
    public function providerCanParse(): Generator
    {
        yield 'WP type not set' => [null, true, 'production'];
        yield 'WP type is production' => ['production', true, 'production'];
        yield 'WP type is staging' => ['staging', false, 'staging'];
        yield 'WP type is development' => ['development', false, 'development'];
        yield 'WP type is local' => ['local', false, 'local'];
    }

    /**
     * @covers       \ContextWP\Helpers\EnvironmentTypeParser::guessTypeFromDomain()
     * @dataProvider providerCanGuessTypeFromDomain
     */
    public function testCanGuessTypeFromDomain(
        ?string $hostValue,
        bool $isLocalSite,
        bool $isStagingSite,
        string $expected
    ): void {
        $parser = $this->createPartialMock(EnvironmentTypeParser::class, ['getHost', 'isLocalSite', 'isStagingSite']);

        $parser->expects($this->once())
            ->method('getHost')
            ->willReturn($hostValue);

        $parser->expects(is_string($hostValue) ? $this->once() : $this->never())
            ->method('isLocalSite')
            ->willReturn($isLocalSite);

        $parser->expects(is_string($hostValue) && ! $isLocalSite ? $this->once() : $this->never())
            ->method('isStagingSite')
            ->willReturn($isStagingSite);

        $this->assertSame(
            $expected,
            $this->invokeInaccessibleMethod($parser, 'guessTypeFromDomain')
        );
    }

    /** @see testCanGuessTypeFromDomain */
    public function providerCanGuessTypeFromDomain(): Generator
    {
        yield 'no host value' => [
            'hostValue'     => null,
            'isLocalSite'   => false,
            'isStagingSite' => false,
            'expected'      => 'production',
        ];

        yield 'local site' => [
            'hostValue'     => 'wp.develop',
            'isLocalSite'   => true,
            'isStagingSite' => false,
            'expected'      => 'local',
        ];

        yield 'staging site' => [
            'hostValue'     => 'staging.wp',
            'isLocalSite'   => false,
            'isStagingSite' => true,
            'expected'      => 'staging',
        ];

        yield 'prod site' => [
            'hostValue'     => 'site.contextwp.com',
            'isLocalSite'   => false,
            'isStagingSite' => false,
            'expected'      => 'production',
        ];
    }

    /**
     * @covers       \ContextWP\Helpers\EnvironmentTypeParser::getHost()
     * @dataProvider providerCanGetHost
     */
    public function testCanGetHost($hostValue, ?string $expected): void
    {
        if (is_null($hostValue)) {
            unset($_SERVER['HTTP_HOST']);
        } else {
            $_SERVER['HTTP_HOST'] = $hostValue;
        }

        $this->assertSame(
            $expected,
            $this->invokeInaccessibleMethod(new EnvironmentTypeParser(), 'getHost')
        );
    }

    /** @see testCanGetHost */
    public function providerCanGetHost(): Generator
    {
        yield 'not set' => [null, null];
        yield 'set but not string' => [123, null];
        yield 'set but is an array' => [['test'], null];
        yield 'set with subdomain' => ['www.contextwp.com', 'www.contextwp.com'];
        yield 'set without tld' => ['localhost', 'localhost'];
    }

    /**
     * @covers       \ContextWP\Helpers\EnvironmentTypeParser::isLocalSite()
     * @dataProvider providerCanDetermineLocalSite
     */
    public function testCanDetermineLocalSite(string $host, bool $expected): void
    {
        $this->assertSame(
            $expected,
            $this->invokeInaccessibleMethod(new EnvironmentTypeParser(), 'isLocalSite', $host)
        );
    }

    /** @see testCanDetermineLocalSite */
    public function providerCanDetermineLocalSite(): Generator
    {
        yield 'staging subdomain' => ['staging.contextwp.com', false];
        yield '.local extension' => ['mysite.local', true];
        yield '.develop extension' => ['mysite.develop', true];
        yield 'no extension' => ['develop/mysite', true];
    }

    /**
     * @covers       \ContextWP\Helpers\EnvironmentTypeParser::isStagingSite()
     * @dataProvider providerCanDetermineStagingSite
     */
    public function testCanDetermineStagingSite(string $host, bool $expected): void
    {
        $this->assertSame(
            $expected,
            $this->invokeInaccessibleMethod(new EnvironmentTypeParser(), 'isStagingSite', $host)
        );
    }

    /** @see testCanDetermineStagingSite */
    public function providerCanDetermineStagingSite(): Generator
    {
        yield 'staging subdomain' => ['staging.contextwp.com', true];
        yield 'other subdomain' => ['www.contextwp.com', false];
        yield 'no subdomain' => ['contextwp.com', false];
    }
}
