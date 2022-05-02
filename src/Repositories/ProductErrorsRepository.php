<?php
/**
 * ProductErrorsRepository.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace ContextWP\Repositories;

use Ashleyfae\WPDB\DB;
use ContextWP\Database\Tables\ProductErrorsTable;
use ContextWP\ValueObjects\ErrorConsequence;
use ContextWP\ValueObjects\Product;

class ProductErrorsRepository
{
    /** @var ProductErrorsTable $productErrorsTable */
    protected $productErrorsTable;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productErrorsTable = new ProductErrorsTable();
    }

    /**
     * Returns the name of the table.
     *
     * @since 1.0
     *
     * @return string
     */
    protected function getTableName(): string
    {
        return DB::applyPrefix($this->productErrorsTable->getTableName());
    }

    /**
     * Returns the current time.
     *
     * @return string
     */
    protected function getNow(): string
    {
        return gmdate('Y-m-d H:i:s');
    }

    /**
     * Deletes expired error records.
     *
     * @since 1.0
     */
    public function deleteExpiredErrors(): void
    {
        DB::query(DB::prepare(
            "DELETE FROM {$this->getTableName()} WHERE locked_until IS NOT NULL AND locked_until < %s",
            $this->getNow()
        ));
    }

    /**
     * Returns the IDs of products that are currently locked.
     *
     * @return array
     */
    public function getLockedProductIds(): array
    {
        return DB::get_col("SELECT product_id FROM {$this->getTableName()}");
    }

    /**
     * Builds the database insert strings for each product consequence.
     *
     * @since 1.0
     *
     * @param  ErrorConsequence[]  $productConsequences
     *
     * @return array
     */
    protected function makeLockProductStrings(array $productConsequences): array
    {
        $values = [];

        foreach ($productConsequences as $product) {
            /*
             * This is dumb, but wpdb::prepare() was converting `null` to an empty string and breaking everything...
             * So we have two routes here: if there's no lock-date then we use one string with a hard-coded `null`;
             * if we have a lock date then we build a separate string using `%s` for the date placeholder.
             * :eyeroll:
             */
            if (is_null($product->getLockedUntil())) {
                $query = "(%s, %d, null, %s)";
                $args  = [
                    $product->productId,
                    (int) $product->isPermanentlyLocked(),
                    $product->responseBody
                ];
            } else {
                $query = "(%s, %d, %s, %s)";
                $args  = [
                    $product->productId,
                    (int) $product->isPermanentlyLocked(),
                    $product->getLockedUntil(),
                    $product->responseBody
                ];
            }

            $values[] = DB::prepare($query, ...$args);
        }

        return $values;
    }

    /**
     * Inserts product locks.
     *
     * @since 1.0
     *
     * @param  ErrorConsequence[]  $productConsequences
     *
     * @return void
     */
    public function lockProducts(array $productConsequences): void
    {
        $valueString = implode(', ', $this->makeLockProductStrings($productConsequences));

        DB::query(
            "INSERT INTO {$this->getTableName()} (product_id, permanently_locked, locked_until, response_body)
                    VALUES {$valueString}
                    ON DUPLICATE KEY UPDATE
                        permanently_locked = VALUES(permanently_locked),
                        locked_until = VALUES(locked_until),
                        response_body = VALUES(response_body)"
        );
    }
}
