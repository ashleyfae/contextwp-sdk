<?php
/**
 * RegisterCronEvent.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Actions;

class RegisterCronEvent
{
    /**
     * Adds hooks.
     *
     * @since 1.0
     * @internal
     */
    public function init(): void
    {
        add_action('wp', [$this, 'maybeScheduleEvent']);
        add_action('contextwp_checkin', [$this, 'handleEvent']);
    }

    /**
     * Schedules the cron event if it's not already scheduled.
     *
     * @since 1.0
     * @internal
     */
    public function maybeScheduleEvent(): void
    {
        if (! wp_next_scheduled('contextwp_checkin')) {
            wp_schedule_event(time(), 'daily', 'contextwp_checkin');
        }
    }

    /**
     * Callback for the cron event.
     *
     * @since 1.0
     * @internal
     */
    public function handleEvent(): void
    {

    }
}
