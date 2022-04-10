<?php
/**
 * ProductErrorsRepository.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Repositories;

use Ashleyfae\WPDB\DB;
use ContextWP\Database\Tables\ProductErrorsTable;
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
            "DELETE FROM {$this->getTableName()} WHERE locked_until < %s",
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
        return DB::get_col(DB::prepare(
            "SELECT product_id FROM {$this->getTableName()} WHERE permanently_locked = 0 AND locked_until <= %s",
            $this->getNow()
        ));
    }

    public function lockProducts(array $products): void
    {
        $lockedUntil = gmdate('Y-m-d H:i:s', '+1 week'); // @todo dynamic reason
        $values = [];

        foreach($products as $product) {
            $values[] = DB::prepare("%s, %s", $product->product_id, $lockedUntil);
        }
    }
}
