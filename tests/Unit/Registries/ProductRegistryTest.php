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
use ReflectionException;

class ProductRegistryTest extends TestCase
{
    /**
     * @covers \ContextWP\Registries\ProductRegistry::add()
     */
    public function testAddProduct(): void
    {
        $registry = new ProductRegistry();
        $product  = new Product('public-key', 'my-product');
        $registry->add($product);

        $this->assertSame(
            ['public-key' => [$product]],
            $registry->getProducts()
        );
    }

    /**
     * @covers \ContextWP\Registries\ProductRegistry::getProducts()
     * @throws ReflectionException
     */
    public function testGetProducts(): void
    {
        $registry = new ProductRegistry();

        $products = [
            'public-key' => [new Product('public-key', 'my-product')]
        ];

        $this->setInaccessibleProperty($registry, 'products', $products);

        $this->assertSame($products, $registry->getProducts());
    }
}
