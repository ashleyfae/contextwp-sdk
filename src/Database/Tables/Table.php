<?php
/**
 * Table.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Database\Tables;

use ContextWP\Contracts\DatabaseTable;
use ContextWP\Database\DB;

abstract class Table implements DatabaseTable
{
    /**
     * @inheritDoc
     */
    public function exists(): bool
    {
        return (bool) DB::getInstance()->get_var(
            DB::getInstance()->prepare(
                "SHOW TABLES LIKE %s",
                DB::getInstance()->esc_like(DB::applyPrefix($this->getTableName()))
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function updateOrCreate(): void
    {
        DB::delta(
            $this->getTableName(),
            $this->getSchema()
        );

        $this->setDbVersion($this->getVersion());
    }

    /**
     * @inheritDoc
     */
    public function drop(): void
    {
        $tableName = DB::applyPrefix($this->getTableName());

        DB::query(
            "DROP TABLE IF EXISTS {$tableName}"
        );
    }

    /**
     * Retrieves the option_name for where we store the database version.
     *
     * @return string
     */
    protected function getVersionOptionName(): string
    {
        return $this->getTableName().'_db_version';
    }

    /**
     * @inheritDoc
     */
    public function getDbVersion(): ?int
    {
        $version = get_option($this->getVersionOptionName());

        return ! empty($version) ? (int) $version : null;
    }

    /**
     * @inheritDoc
     */
    public function setDbVersion(int $version): void
    {
        update_option($this->getVersionOptionName(), $version);
    }

    /**
     * @inheritDoc
     */
    public function needsUpgrade(): bool
    {
        $dbVersion = $this->getDbVersion();

        return empty($dbVersion) || version_compare($dbVersion, $this->getVersion(), '<');
    }
}
