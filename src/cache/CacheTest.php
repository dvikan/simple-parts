<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testNullCache()
    {
        $sut = new NullCache();

        self::assertFalse($sut->has('key1'));
        $sut->set('key1', 'val1');
        self::assertFalse($sut->has('key1'));
        self::assertSame(null, $sut->get('key1'));

        $sut->set('key2', 'val2');
        $sut->delete('key2');
        self::assertFalse($sut->has('key2'));
        self::assertSame('default', $sut->get('key2', 'default'));

        $sut->set('key3');
        self::assertFalse($sut->has('key3'));
        self::assertSame(null, $sut->get('key3'));

        self::assertSame(null, $sut->get('key4'));
        self::assertSame('default', $sut->get('key4', 'default'));
    }

    public function testMemoryCache()
    {
        $sut = new MemoryCache();

        self::assertFalse($sut->has('key1'));
        $sut->set('key1', 'val1');
        self::assertTrue($sut->has('key1'));
        self::assertSame('val1', $sut->get('key1'));

        $sut->set('key2', 'val2');
        $sut->delete('key2');
        self::assertFalse($sut->has('key2'));
        self::assertSame('default', $sut->get('key2', 'default'));

        $sut->set('key3');
        self::assertTrue($sut->has('key3'));
        self::assertSame(true, $sut->get('key3'));

        self::assertSame(null, $sut->get('key4'));
        self::assertSame('default', $sut->get('key4', 'default'));
    }

    public function testFileCache()
    {
        $sut = new FileCache(new NullFile());

        self::assertFalse($sut->has('key1'));
        $sut->set('key1', 'val1');
        self::assertFalse($sut->has('key1'));
        self::assertSame(null, $sut->get('key1'));

        $sut->set('key2', 'val2');
        $sut->delete('key2');
        self::assertFalse($sut->has('key2'));
        self::assertSame('default', $sut->get('key2', 'default'));

        $sut->set('key3');
        self::assertFalse($sut->has('key3'));
        self::assertSame(null, $sut->get('key3'));

        self::assertSame(null, $sut->get('key4'));
        self::assertSame('default', $sut->get('key4', 'default'));

        $sut = new FileCache(new MemoryFile());

        self::assertFalse($sut->has('key1'));
        $sut->set('key1', 'val1');
        self::assertTrue($sut->has('key1'));
        self::assertSame('val1', $sut->get('key1'));

        $sut->set('key2', 'val2');
        $sut->delete('key2');
        self::assertFalse($sut->has('key2'));
        self::assertSame('default', $sut->get('key2', 'default'));

        $sut->set('key3');
        self::assertTrue($sut->has('key3'));
        self::assertSame(true, $sut->get('key3'));

        self::assertSame(null, $sut->get('key4'));
        self::assertSame('default', $sut->get('key4', 'default'));

        $sut = new FileCache(new ArrayFile());

        self::assertFalse($sut->has('key1'));
        $sut->set('key1', 'val1');
        self::assertTrue($sut->has('key1'));
        self::assertSame('val1', $sut->get('key1'));

        $sut->set('key2', 'val2');
        $sut->delete('key2');
        self::assertFalse($sut->has('key2'));
        self::assertSame('default', $sut->get('key2', 'default'));

        $sut->set('key3');
        self::assertTrue($sut->has('key3'));
        self::assertSame(true, $sut->get('key3'));

        self::assertSame(null, $sut->get('key4'));
        self::assertSame('default', $sut->get('key4', 'default'));

        $sut = new FileCache(new LineFile(tempnam(sys_get_temp_dir(), 'CacheTest_')));

        self::assertFalse($sut->has('key1'));
        $sut->set('key1', 'val1');
        self::assertTrue($sut->has('key1'));
        self::assertSame('val1', $sut->get('key1'));

        $sut->set('key2', 'val2');
        $sut->delete('key2');
        self::assertFalse($sut->has('key2'));
        self::assertSame('default', $sut->get('key2', 'default'));

        $sut->set('key3');
        self::assertTrue($sut->has('key3'));
        self::assertSame(true, $sut->get('key3'));

        self::assertSame(null, $sut->get('key4'));
        self::assertSame('default', $sut->get('key4', 'default'));

        $sut = new FileCache(new JsonFile(tempnam(sys_get_temp_dir(), 'CacheTest_')));

        self::assertFalse($sut->has('key1'));
        $sut->set('key1', 'val1');
        self::assertTrue($sut->has('key1'));
        self::assertSame('val1', $sut->get('key1'));

        $sut->set('key2', 'val2');
        $sut->delete('key2');
        self::assertFalse($sut->has('key2'));
        self::assertSame('default', $sut->get('key2', 'default'));

        $sut->set('key3');
        self::assertTrue($sut->has('key3'));
        self::assertSame(true, $sut->get('key3'));

        self::assertSame(null, $sut->get('key4'));
        self::assertSame('default', $sut->get('key4', 'default'));
    }
}
