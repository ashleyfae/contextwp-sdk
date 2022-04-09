<?php
/**
 * DB.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Database;

/**
 * A wrapper for wpdb to:
 *     1. Aid in testing; and
 *     2. Avoid the awful wpdb global.
 */
class DB
{
    /**
     * Returns a wpdb instance.
     *
     * @return \wpdb
     */
    public static function getInstance(): \wpdb
    {
        global $wpdb;

        return $wpdb;
    }

    /**
     * Performs a database query.
     *
     * @param  string  $query
     *
     * @return mixed
     */
    public static function query(string $query)
    {
        return static::getInstance()->query($query);
    }

    public static function applyPrefix(string $tableName): string
    {
        return static::getInstance()->prefix.$tableName;
    }

    public static function delta(string $tableName, string $createTableStatement): void
    {
        require_once ABSPATH.'wp-admin/includes/upgrade.php';

        $tableName = static::applyPrefix($tableName);
        $charset   = static::getInstance()->charset;
        $collate   = static::getInstance()->collate;

        dbDelta(
            "CREATE TABLE {$tableName} ({$createTableStatement} DEFAULT CHARACTER SET {$charset} COLLATE {$collate};)"
        );
    }
}
