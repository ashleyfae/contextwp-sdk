<?php
/**
 * SendCheckInsCommand.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace ContextWP\Cli\Commands;

use ContextWP\Actions\SendCheckIns;
use ContextWP\Contracts\CliCommand;
use Exception;
use WP_CLI;

class SendCheckInsCommand implements CliCommand
{
    /** @var SendCheckIns $sendCheckIns */
    protected $sendCheckIns;

    /**
     * @inheritDoc
     */
    public static function commandName(): string
    {
        return 'checkin';
    }

    public function __invoke(array $args, array $assocArgs): void
    {
        try {
            $this->sendCheckIns->execute();
        } catch (Exception $e) {
            WP_CLI::error(sprintf('%s: %s', get_class($e), $e->getMessage()));
        }
    }
}
