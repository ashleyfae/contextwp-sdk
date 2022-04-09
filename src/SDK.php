<?php
/**
 * SDK.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace ContextWP;

use ContextWP\Database\TableManager;
use ContextWP\Registries\ProductRegistry;
use ContextWP\ValueObjects\Product;

class SDK
{
    /** @var SDK */
    protected static $instance;

    /** @var string Current version. */
    public static $version = '1.0';

    /** @var string Path to the SDK directory. */
    public static $dir;

    /** @var ProductRegistry Contains all registered products. */
    protected $productRegistry;

    /** @var string[] components to initialize and boot */
    protected $components = [
        TableManager::class,
    ];

    /**
     * Returns an instance of the SDK.
     *
     * @since 1.0
     *
     * @return SDK
     */
    public static function instance(): SDK
    {
        if (static::$instance instanceof SDK) {
            return self::$instance;
        }

        static::$instance = new static;
        static::$instance->init();

        return static::$instance;
    }

    /**
     * Initializes things.
     */
    protected function init(): void
    {
        $this->productRegistry = new ProductRegistry();
        $this->loadComponents();
    }

    /**
     * Loads the components. This creates a new instance of the class and calls the `load()` method to
     * do any bootstrapping.
     */
    protected function loadComponents(): void
    {
        foreach ($this->components as $component) {
            (new $component)->load();
        }
    }

    /**
     * Registers a new product.
     *
     * @param  Product  $product
     *
     * @return $this
     */
    public function register(Product $product): SDK
    {
        $this->productRegistry->add($product);

        return $this;
    }
}
