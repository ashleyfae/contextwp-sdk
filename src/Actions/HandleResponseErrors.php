<?php
/**
 * HandleResponseErrors.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Actions;

use ContextWP\Helpers\Str;
use ContextWP\Http\Response;
use ContextWP\Repositories\ProductErrorsRepository;
use ContextWP\ValueObjects\ErrorConsequence;
use ContextWP\ValueObjects\Product;

class HandleResponseErrors
{
    /** @var ProductErrorsRepository $productErrorsRepository */
    protected $productErrorsRepository;

    /** @var Response $response API response */
    protected $response;

    public function __construct()
    {
        $this->productErrorsRepository = new ProductErrorsRepository();
    }

    /**
     * Parses errors out of the response and handles them accordingly for each product it affects.
     *
     * @param  Response  $response
     * @param  array  $products
     *
     * @return void
     */
    public function execute(Response $response, array $products): void
    {
        $this->response = $response;

        if ($code = $response->jsonKey('error_code')) {
            $this->addConsequenceCodeForAll($code, $products);
        } elseif ($response->jsonKey('message') && $response->jsonKey('errors')) {
            $this->addConsequenceCodeForAll(ErrorConsequence::ValidationError, $products);
        } elseif (! is_null($response->jsonKey('accepted')) && $errors = $response->jsonKey('errors')) {
            $this->addIndividualProductConsequences($errors);
        }
    }

    /**
     * Adds the same consequence code for all products. This is called when the _entire_ request fails
     * and all products are affected.
     *
     * @since 1.0
     *
     * @param  string  $errorCode
     * @param  array  $products
     */
    protected function addConsequenceCodeForAll(string $errorCode, array $products): void
    {
        $consequences = array_map(function (Product $product) use ($errorCode) {
            return new ErrorConsequence($product->productId, $errorCode, Str::sanitize($this->response->responseBody));
        }, $products);

        $this->productErrorsRepository->lockProducts($consequences);
    }

    /**
     * Adds consequences for the products included in the errors array. This is called when only specific
     * products fail but the overall request succeeded.
     *
     * @since 1.0
     *
     * @param  array  $errors
     */
    protected function addIndividualProductConsequences(array $errors): void
    {
        $consequences = [];
        foreach ($errors as $productId => $errorCode) {
            $consequences[] = new ErrorConsequence(
                $productId,
                $errorCode,
                Str::sanitize($this->response->responseCode)
            );
        }

        $this->productErrorsRepository->lockProducts($consequences);
    }
}
