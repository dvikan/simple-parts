<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class RouterTest extends TestCase
{
    public function test()
    {
        $sut = new Router();

        $sut->get('/', 'index');
        $sut->get('/about', 'about');
        $sut->get('/user/(\d+)', 'user');
        $sut->get('/user/(\d+)/(\d+)', 'user');
        $sut->post('/delete', 'delete');
        $sut->map(['GET', 'POST'], '/login', 'login');

        $cases = [
            [['GET', '/about'], [Router::FOUND, 'about', []]],
            [['GET', '/'], [Router::FOUND, 'index', []]],
            [['GET', '/user/1'], [Router::FOUND, 'user', ['1']]],
            [['GET', '/user/2/3'], [Router::FOUND, 'user', ['2', '3']]],
            [['POST', '/delete'], [Router::FOUND, 'delete', []]],
            [['GET', '/login'], [Router::FOUND, 'login', []]],
            [['POST', '/login'], [Router::FOUND, 'login', []]],
            [['GET', '/foo'], [Router::NOT_FOUND]],
            [['DELETE', '/'], [Router::METHOD_NOT_ALLOWED]],
        ];

        foreach ($cases as $case) {
            $this->assertSame($case[1], $sut->dispatch(...$case[0]));
        }
    }
}