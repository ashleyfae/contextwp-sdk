<?php
/**
 * Environment.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\ValueObjects;

use ContextWP\Contracts\Arrayable;
use ContextWP\SDK;

class Environment implements Arrayable
{

    public function toArray(): array
    {
        return [
            'source_hash' => '',
            'wp_version'  => '',
            'php_version' => '',
            'locale'      => '',
            'sdk_version' => SDK::getVersion(),
        ];
    }
}
