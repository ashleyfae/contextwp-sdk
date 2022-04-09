<?php
/**
 * Str.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Helpers;

class Str
{
    /**
     * Sanitizes a string.
     *
     * @param  mixed  $string
     *
     * @return string
     */
    public static function sanitize($string): string
    {
        $string = strip_tags((string) $string);

        return function_exists('sanitize_text_field') ? sanitize_text_field($string) : $string;
    }
}
