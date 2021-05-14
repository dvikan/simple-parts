<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class ResolverTest extends TestCase
{
    function test()
    {
        $sut = new Resolver;

        $handler = [foo1::class, 'bar1'];

        $this->assertSame($handler, $sut->resolve($handler));
    }

    function test_callable_object_and_method()
    {
        $sut = new Resolver;

        $handler = [new foo2, 'bar2'];

        $this->assertSame($handler, $sut->resolve($handler));
    }

    function test_invokable()
    {
        $sut = new Resolver;

        $handler = new foo3;

        $this->assertSame([$handler, '__invoke'], $sut->resolve($handler));
    }

    function test_invokable_by_string()
    {
        $sut = new Resolver;

        $handler = foo3::class;

        $this->assertSame([$handler, '__invoke'], $sut->resolve($handler));
    }

    function test_closure()
    {
        $sut = new Resolver;

        $handler = function () { };

        $this->assertSame([$handler, '__invoke'], $sut->resolve($handler));
    }

    function test_non_existing_class()
    {
        $sut = new Resolver;

        $handler = ['non_existing_class', 'foo'];

        $this->expectException(SimpleException::class);

        $_ = $sut->resolve($handler);
    }

    function test_non_existing_method()
    {
        $sut = new Resolver;

        $handler = [foo1::class, 'aaaa'];

        $this->expectException(SimpleException::class);

        $_ = $sut->resolve($handler);
    }
}

class foo1
{
    function bar1() {}
}

class foo2
{
    function bar2() {}
}

class foo3
{
    function __invoke() { }
}