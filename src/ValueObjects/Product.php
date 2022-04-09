<?php
/**
 * Product.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\ValueObjects;

class Product
{
    /** @var string Product UUID */
    public $uuid;

    /**
     * @param  string  $uuid  Product UUID.
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }
}
