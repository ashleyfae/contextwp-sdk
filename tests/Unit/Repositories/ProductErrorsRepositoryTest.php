<?php
/**
 * ProductErrorsRepositoryTest.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Tests\Unit\Repositories;

use Ashleyfae\WPDB\DB;
use ContextWP\Repositories\ProductErrorsRepository;
use ContextWP\Tests\TestCase;
use ReflectionException;

class ProductErrorsRepositoryTest extends TestCase
{
    /**
     * @covers \ContextWP\Repositories\ProductErrorsRepository::deleteExpiredErrors()
     *
     * @throws ReflectionException
     */
    public function testCanDeleteExpiredErrors(): void
    {
        $repository = $this->createPartialMock(ProductErrorsRepository::class, ['getNow']);
        $this->setInaccessibleProperty($repository, 'tableName', 'wp_contextwp_table');

        $repository->expects($this->once())
            ->method('getNow')
            ->willReturn('2022-04-10 12:57:00');

        $dbMock = $this->mockStatic(DB::class);

        $dbMock->shouldReceive('prepare')
            ->once()
            ->with(
                "DELETE FROM wp_contextwp_table WHERE locked_until < %s",
                '2022-04-10 12:57:00'
            )
            ->andReturn("DELETE FROM wp_contextwp_table WHERE locked_until < '2022-04-10 12:57:00'");

        $dbMock->shouldReceive('query')
            ->once()
            ->with("DELETE FROM wp_contextwp_table WHERE locked_until < '2022-04-10 12:57:00'")
            ->andReturnNull();

        $repository->deleteExpiredErrors();
    }

    /**
     * @covers \ContextWP\Repositories\ProductErrorsRepository::getLockedProductIds()
     * @throws ReflectionException
     */
    public function testCanGetProductIdsWithErrors(): void
    {
        $repository = $this->createPartialMock(ProductErrorsRepository::class, ['getNow']);
        $this->setInaccessibleProperty($repository, 'tableName', 'wp_contextwp_table');

        $repository->expects($this->once())
            ->method('getNow')
            ->willReturn('2022-04-10 12:57:00');

        $dbMock = $this->mockStatic(DB::class);

        $dbMock->shouldReceive('prepare')
            ->once()
            ->with(
                "SELECT product_id FROM wp_contextwp_table WHERE permanently_locked = 0 AND locked_until <= %s",
                '2022-04-10 12:57:00'
            )
            ->andReturn("SELECT product_id FROM wp_contextwp_table WHERE permanently_locked = 0 AND locked_until <= '2022-04-10 12:57:00'");

        $dbMock->shouldReceive('get_col')
            ->once()
            ->with("SELECT product_id FROM wp_contextwp_table WHERE permanently_locked = 0 AND locked_until <= '2022-04-10 12:57:00'")
            ->andReturn(['id-1', 'id-2']);

        $this->assertSame(['id-1', 'id-2'], $repository->getLockedProductIds());
    }
}
