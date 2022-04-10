<?php
/**
 * UpdateCheckInSchedule.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace ContextWP\Actions;

use ContextWP\Traits\Makeable;

class UpdateCheckInSchedule
{
    use Makeable;

    /**
     * Regular interval used for successful requests.
     */
    const REGULAR_INTERVAL = '+1 week';

    /**
     * Interval used when the service is temporarily unavailable.
     */
    const SERVICE_UNAVAILABLE = '+1 day';

    public function setNextCheckIn(?string $period = null)
    {
        $period = $period ?: static::REGULAR_INTERVAL;
    }
}
