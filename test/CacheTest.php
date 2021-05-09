<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class CacheTest extends TestCase
{
    /**
     * @var Cache
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new Cache(new MemoryFile());
    }

    public function test_set_and_get()
    {
        $this->sut->set('foo1');
        $this->sut->set('foo2', 'bar');
        $this->sut->set('foo3', [1, 2]);

        $this->assert(null, $this->sut->get('non'));
        $this->assert('def', $this->sut->get('non', 'def'));
        $this->assert(['def'], $this->sut->get('non', ['def']));
        $this->assert(true, $this->sut->get('foo1'));
        $this->assert('bar', $this->sut->get('foo2'));
        $this->assert('bar', $this->sut->get('foo2', 'def'));
        $this->assert([1, 2], $this->sut->get('foo3'));
    }

    public function test_delete()
    {
        $this->sut->set('foo', 'bar');

        $this->sut->delete('foo');

        $this->assert(null, $this->sut->get('foo'));
        $this->assert(1, $this->sut->get('foo', 1));
    }

    public function test_clear()
    {
        $this->sut->set('foo1');
        $this->sut->set('foo2');

        $this->sut->clear();

        $this->assert(null, $this->sut->get('foo1'));
        $this->assert(null, $this->sut->get('foo2'));
    }

    public function test_expire()
    {
        $clock = new FrozenClock(new \DateTimeImmutable());
        $sut = new Cache(new MemoryFile(), $clock);
        $sut->set('foo', 'bar', 10);

        $clock->advance(new \DateInterval('PT11S'));

        $this->assert(null, $sut->get('foo'));
    }
}