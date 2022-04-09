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
use ContextWP\Helpers\SourceHasher;
use ContextWP\SDK;

class Environment implements Arrayable
{
    public function toArray(): array
    {
        return [
            'source_hash' => $this->getSourceHash(),
            'wp_version'  => '',
            'php_version' => '',
            'locale'      => '',
            'sdk_version' => SDK::getVersion(),
        ];
    }

    protected function getSourceHash(): string
    {
        return (new SourceHasher())->getHash();
    }
}
