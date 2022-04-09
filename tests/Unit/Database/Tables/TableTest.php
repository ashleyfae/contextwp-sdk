<?php
/**
 * TableTest.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Tests\Unit\Database\Tables;

use ContextWP\Database\DB;
use ContextWP\Database\Tables\Table;
use ContextWP\Tests\TestCase;
use Generator;
use Mockery\MockInterface;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use WP_Mock;

class TableTest extends TestCase
{
    /**
     * Returns a mock of the abstract Table class.
     *
     * @param  array  $methods  Methods to mock.
     *
     * @return Table|MockObject
     */
    protected function getMock(array $methods = [])
    {
        return $this->getMockForAbstractClass(
            Table::class,
            [],
            '',
            true,
            true,
            true,
            $methods
        );
    }

    /**
     * @covers \ContextWP\Database\Tables\Table::updateOrCreate()
     */
    public function testCanUpdateOrCreate()
    {
        $table = $this->getMock(['getTableName', 'getSchema', 'setDbVersion', 'getVersion']);

        $table->expects($this->once())
            ->method('getTableName')
            ->willReturn('contextwp_table');

        $table->expects($this->once())
            ->method('getSchema')
            ->willReturn('schema');

        $table->expects($this->once())
            ->method('setDbVersion')
            ->with(4567)
            ->willReturn(null);

        $table->expects($this->once())
            ->method('getVersion')
            ->willReturn(4567);

        /** @var DB&MockInterface $db */
        $db = $this->mockStatic(DB::class);
        $db->shouldReceive('delta')
            ->with('contextwp_table', 'schema')
            ->andReturnNull();

        $table->updateOrCreate();
    }

    /**
     * @covers \ContextWP\Database\Tables\Table::drop()
     */
    public function getCanDrop()
    {
        $table = $this->getMock(['getTableName']);

        $table->expects($this->once())
            ->method('getTableName')
            ->willReturn('contextwp_table');

        /** @var DB&MockInterface $db */
        $db = $this->mockStatic(DB::class);

        $db->shouldReceive('applyPrefix')
            ->with('contextwp_table')
            ->andReturn('wp_contextwp_table');

        $db->shouldReceive('query')
            ->with('DROP TABLE IF EXISTS wp_contextwp_table')
            ->andReturnNull();
    }

    /**
     * @covers \ContextWP\Database\Tables\Table::getVersionOptionName()
     * @throws ReflectionException
     */
    public function testCanGetVersionOptionName(): void
    {
        $table = $this->getMock(['getTableName']);

        $table->expects($this->once())
            ->method('getTableName')
            ->willReturn('contextwp_table');

        $this->assertSame(
            'contextwp_table_db_version',
            $this->invokeInaccessibleMethod($table, 'getVersionOptionName')
        );
    }

    /**
     * @covers       \ContextWP\Database\Tables\Table::getDbVersion()
     * @dataProvider providerCanGetDbVersion
     */
    public function testCanGetDbVersion($returnedVersion, ?int $expectedVersion): void
    {
        $mock = $this->getMock(['getVersionOptionName']);

        $mock->expects($this->once())
            ->method('getVersionOptionName')
            ->willReturn('contextwp_table_db_version');

        WP_Mock::userFunction('get_option')
            ->with('contextwp_table_db_version')
            ->andReturn($returnedVersion);

        $this->assertSame($expectedVersion, $mock->getDbVersion());
    }

    /** @see testCanGetDbVersion */
    public function providerCanGetDbVersion(): Generator
    {
        yield 'option returns false' => [
            'returnedVersion' => false,
            'expectedVersion' => null,
        ];

        yield 'option returns empty string' => [
            'returnedVersion' => '',
            'expectedVersion' => null,
        ];

        yield 'option returns integer as string' => [
            'returnedVersion' => '123',
            'expectedVersion' => 123,
        ];

        yield 'option returns integer' => [
            'returnedVersion' => 456,
            'expectedVersion' => 456,
        ];
    }

    /**
     * @covers       \ContextWP\Database\Tables\Table::setDbVersion()
     */
    public function testCanSetDbVersion(): void
    {
        $mock = $this->getMock(['getVersionOptionName']);

        $mock->expects($this->once())
            ->method('getVersionOptionName')
            ->willReturn('contextwp_table_db_version');

        WP_Mock::userFunction('update_option')
            ->with('contextwp_table_db_version', 4567)
            ->andReturn(true);

        $mock->setDbVersion(4567);

        $this->assertConditionsMet();
    }

    /**
     * @covers       \ContextWP\Database\Tables\Table::needsUpgrade()
     * @dataProvider providerNeedsUpgrade
     */
    public function testNeedsUpgrade(?int $dbVersion, int $currentVersion, bool $expected): void
    {
        $table = $this->getMock(['getDbVersion', 'getVersion']);

        $table->expects($this->once())
            ->method('getDbVersion')
            ->willReturn($dbVersion);

        $table->expects(! empty($dbVersion) ? $this->once() : $this->never())
            ->method('getVersion')
            ->willReturn($currentVersion);

        $this->assertSame($expected, $table->needsUpgrade());
    }

    /** @see testNeedsUpgrade */
    public function providerNeedsUpgrade(): Generator
    {
        $currentVersion = time();

        yield 'no database version' => [
            'dbVersion'      => null,
            'currentVersion' => $currentVersion,
            'expected'       => true,
        ];

        yield 'out of date version' => [
            'dbVersion'      => strtotime('-1 day'),
            'currentVersion' => $currentVersion,
            'expected'       => true,
        ];

        yield 'same version' => [
            'dbVersion'      => $currentVersion,
            'currentVersion' => $currentVersion,
            'expected'       => false,
        ];
    }
}
