<?php
/**
 * Response.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Http;

use WP_Error;

class Response
{
    /** @var int HTTP response code */
    public $responseCode;

    /** @var string|null response body */
    public $responseBody;

    public function __construct(int $responseCode, ?string $body = null)
    {
        $this->responseCode = $responseCode;
        $this->responseBody = $body;
    }

    /**
     * Creates a new response object from a WP_Error object.
     *
     * @param  WP_Error  $error
     *
     * @return Response
     */
    public static function makeFromWpError(WP_Error $error): Response
    {
        return new static(503, $error->get_error_message() ?: null);
    }

    /**
     * If the response is okay. This does NOT necessarily mean every single product got a check-in, but
     * it means we passed any authorization requirements, and were able to talk to the site.
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->responseCode >= 200 && $this->responseCode < 300;
    }

    /**
     * Determines if we have any errors.
     *
     * @todo implement
     *
     * @return bool
     */
    public function hasErrors(): bool
    {

    }
}
