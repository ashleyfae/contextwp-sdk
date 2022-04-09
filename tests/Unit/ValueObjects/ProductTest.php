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
        $product = new Product('my-product');

        $this->assertSame(
            'my-product',
            $this->getInaccessibleProperty($product, 'uuid')->getValue($product)
        );
    }
}
