<?php

/**
 * @see       https://github.com/laminas/laminas-memory for the canonical source repository
 * @copyright https://github.com/laminas/laminas-memory/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-memory/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Memory;

use Laminas\Cache\Storage\Adapter\AdapterInterface as CacheAdapter;
use Laminas\Cache\StorageFactory as CacheFactory;
use Laminas\Memory;
use PHPUnit\Framework\TestCase;

/**
 * @group      Laminas_Memory
 */
class AccessControllerTest extends TestCase
{
    /**
     * Cache object
     *
     * @var CacheAdapter
     */
    private $cache = null;

    public function setUp()
    {
        $this->cache = CacheFactory::adapterFactory('memory', ['memory_limit' => 0]);
    }

    /**
     * tests the Movable memory container object creation
     */
    public function testCreation()
    {
        $memoryManager  = new Memory\MemoryManager($this->cache);
        $memObject      = $memoryManager->create('012345678');

        $this->assertInstanceOf(Memory\Container\AccessController::class, $memObject);
    }

    /**
     * tests the value access methods
     */
    public function testValueAccess()
    {
        $memoryManager  = new Memory\MemoryManager($this->cache);
        $memObject      = $memoryManager->create('0123456789');

        // getRef() method
        $this->assertEquals($memObject->getRef(), '0123456789');

        $valueRef = &$memObject->getRef();
        $valueRef[3] = '_';
        $this->assertEquals($memObject->getRef(), '012_456789');

        // value property
        $this->assertEquals((string) $memObject->value, '012_456789');

        $memObject->value[7] = '_';
        $this->assertEquals((string) $memObject->value, '012_456_89');

        $memObject->value = 'another value';
        $this->assertInstanceOf(Memory\Value::class, $memObject->value);
        $this->assertEquals((string) $memObject->value, 'another value');
    }

    /**
     * tests lock()/unlock()/isLocked() functions
     */
    public function testLock()
    {
        $memoryManager  = new Memory\MemoryManager($this->cache);
        $memObject      = $memoryManager->create('012345678');

        $this->assertFalse((bool) $memObject->isLocked());

        $memObject->lock();
        $this->assertTrue($memObject->isLocked());

        $memObject->unlock();
        $this->assertFalse($memObject->isLocked());
    }
}
