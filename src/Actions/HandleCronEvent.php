<?php
/**
 * HandleCronEvent.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace ContextWP\Actions;

use ContextWP\Contracts\Component;
use ContextWP\Repositories\CheckInScheduleRepository;
use Exception;

/**
 * Registers and handles the cron event to send check-ins.
 */
class HandleCronEvent implements Component
{
    const EVENT_HOOK = 'contextwp_checkin';

    /** @var CheckInScheduleRepository $checkInRepository */
    protected $checkInRepository;

    /** @var SendCheckIns $sendCheckIns */
    protected $sendCheckIns;

    public function __construct()
    {
        $this->checkInRepository = new CheckInScheduleRepository();
        $this->sendCheckIns      = new SendCheckIns();
    }

    /**
     * Adds hooks.
     *
     * @since 1.0
     * @internal
     */
    public function load(): void
    {
        add_action('wp', [$this, 'maybeScheduleEvent']);
        add_action(static::EVENT_HOOK, [$this, 'handleEvent']);
    }

    /**
     * Schedules the cron event if it's not already scheduled.
     *
     * @since 1.0
     * @internal
     */
    public function maybeScheduleEvent(): void
    {
        if (! wp_next_scheduled(static::EVENT_HOOK)) {
            wp_schedule_event($this->getEventRunTime(), 'daily', static::EVENT_HOOK);
        }
    }

    /**
     * Returns a random timestamp for which the event should be scheduled. This will be a timestamp ranging
     * anywhere from now to 2 hours from now.
     *
     * @since 1.0
     * @return int
     */
    protected function getEventRunTime(): int
    {
        $time = time();

        // add a random amount of time to help stagger events so we don't have all sites checking in at once!
        $numberMinutes = rand(0, 120);

        return $time + ($numberMinutes * 60);
    }

    /**
     * Callback for the cron event.
     *
     * @since 1.0
     * @internal
     */
    public function handleEvent(): void
    {
        if ($this->needsCheckIn()) {
            $this->handleCheckIn();
        }
    }

    /**
     * Determines if the site is ready for a scheduled check-in.
     *
     * @since 1.0
     *
     * @return bool
     */
    protected function needsCheckIn(): bool
    {
        $nextCheckIn = $this->checkInRepository->get();

        return empty($nextCheckIn) || $nextCheckIn <= time();
    }

    /**
     * Sends all check-ins.
     *
     * @since 1.0
     */
    protected function handleCheckIn(): void
    {
        try {
            $this->sendCheckIns->execute();
        } catch (Exception $e) {
            if ($this->shouldLogExceptions()) {
                $this->logException($e);
            }
        }
    }

    /**
     * If exceptions should be logged.
     *
     * @since 1.0
     *
     * @return bool
     */
    protected function shouldLogExceptions(): bool
    {
        return defined('WP_DEBUG') && WP_DEBUG;
    }

    /**
     * Logs an exception.
     *
     * @since 1.0
     *
     * @param  Exception  $e
     */
    protected function logException(Exception $e): void
    {
        error_log(sprintf('%s: %s', get_class($e), $e->getMessage()));
    }
}
