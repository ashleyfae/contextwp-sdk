<?php
/**
 * ResponseTest.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Tests\Unit\Http;

use ContextWP\Http\Response;
use ContextWP\Tests\TestCase;

class ResponseTest extends TestCase
{
    /**
     * @covers \ContextWP\Http\Response::__construct()
     */
    public function testCanConstruct(): void
    {
        $response = new Response(201, 'body');

        $this->assertSame(201, $response->responseCode);
        $this->assertSame('body', $response->responseBody);
    }

    /**
     * @covers \ContextWP\Http\Response::makeFromWpError()
     */
    public function testCanMakeFromWpError(): void
    {
        $error = \Mockery::mock('WP_Error');
        $error->expects('get_error_message')
            ->once()
            ->andReturn('error message');

        $response = Response::makeFromWpError($error);

        $this->assertSame(503, $response->responseCode);
        $this->assertSame('error message', $response->responseBody);
    }

    /**
     * @covers       \ContextWP\Http\Response::isOk()
     * @dataProvider providerIsOk
     */
    public function testIsOk(int $responseCode, bool $expected): void
    {
        $response = new Response($responseCode);

        $this->assertSame($expected, $response->isOk());
    }

    public function providerIsOk(): \Generator
    {
        yield '200 is ok' => [200, true];
        yield '201 is ok' => [201, true];
        yield '204 is ok' => [204, true];
        yield '301 not ok' => [301, false];
        yield '422 not ok' => [422, false];
        yield '500 not ok' => [500, false];
        yield '503 not ok' => [503, false];
    }
}
