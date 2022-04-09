<?php
/**
 * SDKTest.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace ContextWP\Tests\Unit;

use ContextWP\Registries\ProductRegistry;
use ContextWP\SDK;
use ContextWP\Tests\TestCase;
use ContextWP\ValueObjects\Product;
use Mockery;
use ReflectionException;

class SDKTest extends TestCase
{
    /**
     * @covers \ContextWP\SDK::init();
     * @throws ReflectionException
     */
    public function testCanInit(): void
    {
        $sdk = $this->createPartialMock(SDK::class, ['loadComponents']);

        $sdk->expects($this->once())
            ->method('loadComponents')
            ->willReturn(null);

        $this->assertNull($this->getInaccessibleProperty($sdk, 'productRegistry')->getValue($sdk));

        $this->invokeInaccessibleMethod($sdk, 'init');

        $this->assertInstanceOf(
            ProductRegistry::class,
            $this->getInaccessibleProperty($sdk, 'productRegistry')->getValue($sdk)
        );
    }

    /**
     * @covers \ContextWP\SDK::getVersion()
     * @throws ReflectionException
     */
    public function testCanGetVersion(): void
    {
        $sdk = new SDK();

        $this->setInaccessibleProperty($sdk, 'version', '2.5');

        $this->assertSame('2.5', $sdk::getVersion());
    }

    /**
     * @covers \ContextWP\SDK::register()
     * @throws ReflectionException
     */
    public function testCanRegister(): void
    {
        $sdk     = new SDK();
        $product = new Product('public-key','123');

        $registry = Mockery::mock(ProductRegistry::class);
        $registry->expects('add')
            ->once()
            ->with($product);

        $this->setInaccessibleProperty($sdk, 'productRegistry', $registry);

        $sdk->register($product);

        $this->assertConditionsMet();
    }
}
