<?php
/**
 * SendRequest.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Actions;

use ContextWP\Http\Request;
use ContextWP\Http\Response;
use ContextWP\ValueObjects\Environment;
use Exception;

/**
 * Handles executing a request to send check-in data for the supplied products.
 */
class SendRequest
{
    /**
     * Sends the check-in request.
     *
     * @param  array  $products
     *
     * @return Response
     * @throws Exception
     */
    public function execute(array $products): Response
    {
        return Request::make()
            ->setUrl($this->getApiUrl())
            ->setEnvironment(Environment::make())
            ->setProducts($products)
            ->execute();
    }

    /**
     * Returns the API URL.
     *
     * @return string
     */
    protected function getApiUrl(): string
    {
        if (defined('CONTEXTWP_API_URL')) {
            return (string) CONTEXTWP_API_URL;
        } else {
            return 'https://contextwp.com/api/v1/checkin';
        }
    }
}
