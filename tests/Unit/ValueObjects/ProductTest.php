<?php
/**
 * ProductTest.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Tests\Unit\ValueObjects;

use ContextWP\Tests\TestCase;
use ContextWP\ValueObjects\Product;

class ProductTest extends TestCase
{
    /**
     * @covers \ContextWP\ValueObjects\Product::__construct()
     */
    public function testCanConstruct()
    {
        $product = new Product('public-key', 'my-product');

        $this->assertSame(
            'public-key',
            $this->getInaccessibleProperty($product, 'publicKey')->getValue($product)
        );

        $this->assertSame(
            'my-product',
            $this->getInaccessibleProperty($product, 'productId')->getValue($product)
        );
    }
}
