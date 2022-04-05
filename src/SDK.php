<?php
/**
 * SDK.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace ContextWP;

class SDK
{
    /** @var SDK */
    protected static $instance;

    /** @var string Current version. */
    public static $version = '1.0';

    /** @var string Path to the SDK directory. */
    public static $dir;

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
        static::$instance->setupInstance();

        return static::$instance;
    }

    protected function setupInstance(): void
    {

    }
}
