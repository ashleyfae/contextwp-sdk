<?php
/**
 * ProductRegistry.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Registries;

use ContextWP\ValueObjects\Product;

/**
 * Holds all products that have been registered.
 *
 * @since 1.0
 */
class ProductRegistry
{
    /** @var array All products, grouped by public key */
    protected $products = [];

    /**
     * Adds a new product.
     *
     * @since 1.0
     *
     * @param  Product  $product
     *
     * @return $this
     */
    public function add(Product $product): ProductRegistry
    {
        if (! array_key_exists($product->publicKey, $this->products)) {
            $this->products[$product->publicKey] = [];
        }

        $this->products[$product->publicKey][] = $product;

        return $this;
    }

    /**
     * Retrieves all products.
     *
     * @since 1.0
     *
     * @return array
     */
    public function getProducts(): array
    {
        return $this->products;
    }
}
