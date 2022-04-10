<?php
/**
 * Product.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace ContextWP\ValueObjects;

use ContextWP\Contracts\Arrayable;
use ContextWP\Helpers\Str;

class Product implements Arrayable
{
    /** @var string Customer public key */
    public $publicKey;

    /** @var string Product UUID */
    public $productId;

    /** @var null Product version */
    protected $version = null;

    /**
     * @param  string  $productId  Product UUID.
     */
    public function __construct(string $publicKey, string $productId)
    {
        $this->publicKey = $publicKey;
        $this->productId = $productId;
    }

    /**
     * Sets the product version.
     *
     * @param  string  $version
     *
     * @return $this
     */
    public function setVersion(string $version): Product
    {
        $this->version = Str::sanitize($version);

        return $this;
    }

    /**
     * Converts the product to what's expected in the API request.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'product_id'      => $this->productId,
            'product_version' => $this->version,
        ];
    }
}
