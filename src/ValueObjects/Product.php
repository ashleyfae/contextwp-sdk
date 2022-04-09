<?php
/**
 * Product.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\ValueObjects;

use ContextWP\Contracts\Arrayable;
use ContextWP\Helpers\Str;

class Product implements Arrayable
{
    /** @var string Product UUID */
    public $uuid;

    /** @var null Product version */
    protected $version = null;

    /**
     * @param  string  $uuid  Product UUID.
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
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
            'product_id'      => $this->uuid,
            'product_version' => $this->version,
        ];
    }
}
