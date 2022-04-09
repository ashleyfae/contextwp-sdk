<?php
/**
 * ProductRegistry.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Registries;

use ArrayObject;
use ContextWP\ValueObjects\Product;

class ProductRegistry extends ArrayObject
{
    /**
     * Adds a new product.
     *
     * @param  Product  $product
     *
     * @return $this
     */
    public function add(Product $product): ProductRegistry
    {
        $this->offsetSet($product->uuid, $product);

        return $this;
    }
}
