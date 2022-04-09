<?php
/**
 * ProductRegistryTest.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Tests\Unit\Registries;

use ContextWP\Registries\ProductRegistry;
use ContextWP\Tests\TestCase;
use ContextWP\ValueObjects\Product;

class ProductRegistryTest extends TestCase
{
    /**
     * @covers \ContextWP\Registries\ProductRegistry::add()
     */
    public function testAddProduct(): void
    {
        $registry = new ProductRegistry();
        $product  = new Product('my-product');
        $registry->add($product);

        $this->assertSame(
            ['my-product' => $product],
            $registry->getArrayCopy()
        );
    }
}
