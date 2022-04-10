<?php
/**
 * ApiUnavailableException.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Exceptions;

/**
 * Thrown when the API probably isn't working right now and everything should be cancelled and
 * reattempted later.
 */
class ServiceUnavailableException extends \Exception
{

}
