<?php
/**
 * SendCheckInsCommand.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace ContextWP\Cli\Commands;

use ContextWP\Contracts\CliCommand;

class SendCheckInsCommand implements CliCommand
{
    /**
     * @inheritDoc
     */
    public static function commandName(): string
    {
        return 'checkin';
    }

    public function __invoke(array $args, array $assocArgs): void
    {
        // TODO: Implement __invoke() method.
    }
}
