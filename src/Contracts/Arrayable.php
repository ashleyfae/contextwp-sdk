<?php
/**
 * Arrayable.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Contracts;

interface Arrayable
{
    /**
     * Converts the object to an array.
     *
     * @return array
     */
    public function toArray(): array;
}
