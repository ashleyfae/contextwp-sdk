<?php
/**
 * Str.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
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

    public static function isUuid(string $string): bool
    {
        return preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $string) === 1;
    }
}
