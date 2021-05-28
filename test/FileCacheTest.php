<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

use PDO;

final class FileCacheTest extends TestCase
{
    private $sut;

    public function __construct()
    {
        $this->sut = new FileCache(new MemoryFile('./foo'));
    }

    public function test()
    {
        $this->sut->set('foo1');
        $this->sut->set('foo2', 'bar');
        $this->sut->set('foo3', [1, 2]);

        $this->assertSame(null, $this->sut->get('non'));
        $this->assertSame('def', $this->sut->get('non', 'def'));
        $this->assertSame(['def'], $this->sut->get('non', ['def']));
        $this->assertSame(true, $this->sut->get('foo1'));
        $this->assertSame('bar', $this->sut->get('foo2'));
        $this->assertSame('bar', $this->sut->get('foo2', 'def'));
        $this->assertSame([1, 2], $this->sut->get('foo3'));
    }

    public function test_delete()
    {
        $this->sut->set('foo', 'bar');

        $this->sut->delete('foo');

        $this->assertSame(null, $this->sut->get('foo'));
        $this->assertSame(1, $this->sut->get('foo', 1));
    }

    protected function test_clear()
    {
        $this->sut->set('foo1');
        $this->sut->set('foo2');

        $this->sut->clear();

        $this->assertSame(null, $this->sut->get('foo1'));
        $this->assertSame(null, $this->sut->get('foo2'));
    }

    protected function test_expire()
    {
        $clock = new FrozenClock(new \DateTimeImmutable());
        $sut = new FileCache(new MemoryFile('./foo'), $clock);
        $sut->set('foo', 'bar', 10);

        $clock->advance(new \DateInterval('PT11S'));

        $this->assertSame(null, $sut->get('foo'));
    }
}