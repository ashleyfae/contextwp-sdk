<?php
/**
 * ErrorConsequence.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\ValueObjects;

class ErrorConsequence
{
    const MissingAuthenticationHeader = 'missing_auth_header';
    const InvalidAuthenticationHeader = 'invalid_auth_header';
    const ProductNotFound = 'product_not_found';
    const NoActiveSubscription = 'no_active_subscription';
    const AtCheckInLimit = 'at_subscription_limit';

    /** @var string $productId ID of the product */
    public $productId;

    /** @var string $reason Reason for the error */
    public $reason;

    public function __construct(string $productId, string $reason)
    {
        $this->productId = $productId;
        $this->reason    = $reason;
    }

    public function isPermanentlyLocked(): bool
    {
        return in_array($this->reason, [
            static::ProductNotFound
        ], true);
    }

    public function getLockedUntil(): ?string
    {
        switch ($this->reason) {
            case static::ProductNotFound:
                return null;

            default :
                return '+1 week';
        }
    }
}
