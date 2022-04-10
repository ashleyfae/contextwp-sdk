<?php
/**
 * HandleResponseErrorsTest.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Tests\Unit\Actions;

use ContextWP\Actions\HandleResponseErrors;
use ContextWP\Http\Response;
use ContextWP\Repositories\ProductErrorsRepository;
use ContextWP\Tests\TestCase;
use ContextWP\ValueObjects\ErrorConsequence;
use ContextWP\ValueObjects\Product;
use Generator;
use ReflectionException;

class HandleResponseErrorsTest extends TestCase
{
    /**
     * @covers \ContextWP\Actions\HandleResponseErrors::execute()
     * @dataProvider providerCanExecute
     */
    public function testCanExecute(
        string $responseBody,
        bool $sameConsequenceForAll,
        ?string $expectedErrorCode
    ): void {
        $handler = $this->createPartialMock(
            HandleResponseErrors::class,
            ['addConsequenceCodeForAll', 'addIndividualProductConsequences']
        );

        $products = [new Product('public-key', 'pid')];
        $response = new Response(400, $responseBody);

        $handler->expects($sameConsequenceForAll ? $this->once() : $this->never())
            ->method('addConsequenceCodeForAll')
            ->with($expectedErrorCode, $products)
            ->willReturn(null);

        $handler->expects($sameConsequenceForAll ? $this->never() : $this->once())
            ->method('addIndividualProductConsequences')
            ->with($response->jsonKey('errors'))
            ->willReturn(null);

        $handler->execute($response, $products);
    }

    /** @see testCanExecute */
    public function providerCanExecute(): Generator
    {
        yield 'missing auth header' => [
            '{"error_code":"missing_auth_header","error_message":"Missing authentication header."}',
            true,
            'missing_auth_header',
        ];

        yield 'validation error' => [
            '{"message":"The environment field is required. (and 2 more errors)","errors":{"environment":["The environment field is required."],"environment.source_hash":["The environment.source hash field is required."],"products":["The products field is required."]}}',
            true,
            'validation_error',
        ];

        yield 'individual product not found error' => [
            '{"accepted":[],"errors":{"4f9c853d-1baf-4c2f-96cb-1f464ea3680f":"product_not_found"}}',
            false,
            'product_not_found',
        ];
    }

    /**
     * @covers \ContextWP\Actions\HandleResponseErrors::addConsequenceCodeForAll()
     */
    public function testCanAddConsequenceCodeForAll(): void
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \ContextWP\Actions\HandleResponseErrors::addIndividualProductConsequences()
     */
    public function testCanAddProductConsequences(): void
    {
        $this->markTestIncomplete();
    }
}
