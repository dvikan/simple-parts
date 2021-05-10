<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class ConfigTest extends TestCase
{
    public function test()
    {
        $sut = Config::fromArray(['env' => 'dev']);

        $this->assert('dev', $sut->get('env'));
    }

    public function test_custom_config()
    {
        $sut = Config::fromArray(['env' => 'dev'], ['env' => 'prod']);

        $this->assert('prod', $sut->get('env'));
    }
}