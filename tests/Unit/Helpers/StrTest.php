<?php
/**
 * StrTest.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Tests\Unit\Helpers;

use ContextWP\Helpers\Str;
use ContextWP\Tests\TestCase;

class StrTest extends TestCase
{
    /**
     * @covers \ContextWP\Helpers\Str::isUuid()
     * @dataProvider providerIsUuid
     */
    public function testIsUuid(string $string, bool $expected): void
    {
        $this->assertSame($expected, Str::isUuid($string));
    }

    /** @see testIsUuid */
    public function providerIsUuid(): \Generator
    {
        yield 'valid uuid' => ['4f9c853d-1baf-4c2f-96cb-1f464ea3680f', true];
        yield 'invalid uuid' => ['not-a-uuid', false];
        yield 'empty string' => ['', false];
    }
}
