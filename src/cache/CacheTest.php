<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testMemoryCache()
    {
        $this->_testCache(new MemoryCache());
        $this->_testCache(new FileCache(new MemoryFile()));
        $this->_testCache(new FileCache(new TextFile(tempnam(sys_get_temp_dir(), 'FileTest_'))));
    }

    private function _testCache(Cache $sut): void
    {
        $sut->set('key1', 'bar1');
        $sut->set('key2');

        self::assertFalse($sut->has('key0'));
        self::assertTrue($sut->has('key1'));
        self::assertTrue($sut->has('key2'));
        self::assertSame('bar1', $sut->get('key1'));
        $sut->delete('key1');
        self::assertFalse($sut->has('key1'));
        self::assertSame(true, $sut->get('key2'));
        self::assertSame('default', $sut->get('key3', 'default'));
    }
}
