<?php
/**
 * Component.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Contracts;

interface Component
{
    /**
     * Loads the component.
     *
     * @return void
     */
    public function load(): void;
}
