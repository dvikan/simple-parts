<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class RouterTest extends TestCase
{
    public function test()
    {
        $sut = new Router();

        $sut->addRoute('GET', '/', 'index');
        $sut->addRoute('GET', '/about', 'about');
        $sut->addRoute('GET', '/user/(\d+)', 'user');
        $sut->addRoute('GET', '/user/(\d+)/(\d+)', 'user');
        $sut->addRoute('POST', '/delete', 'delete');
        $sut->addRoute(['GET', 'POST'], '/login', 'login');

        $cases = [
            [['GET', '/'],          [Router::FOUND, 'index', []]],
            [['GET', '/about'],     [Router::FOUND, 'about', []]],
            [['GET', '/user/1'],    [Router::FOUND, 'user', ['1']]],
            [['GET', '/user/2/3'],  [Router::FOUND, 'user', ['2', '3']]],
            [['POST', '/delete'],   [Router::FOUND, 'delete', []]],
            [['GET', '/login'],     [Router::FOUND, 'login', []]],
            [['POST', '/login'],    [Router::FOUND, 'login', []]],
            [['GET', '/foo'],       [Router::NOT_FOUND, null, []]],
            [['DELETE', '/'],       [Router::METHOD_NOT_ALLOWED, null, []]],
        ];

        foreach ($cases as $case) {
            $this->assertSame($case[1], $sut->dispatch(...$case[0]));
        }
    }
}