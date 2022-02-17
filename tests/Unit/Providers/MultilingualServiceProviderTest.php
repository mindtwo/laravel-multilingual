<?php

namespace mindtwo\LaravelMultilingual\Tests\Unit\Providers;

use Illuminate\Support\Facades\Config;
use mindtwo\LaravelMultilingual\Providers\MultilingualServiceProvider;
use mindtwo\LaravelMultilingual\Tests\TestCase;

class MultilingualServiceProviderTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [MultilingualServiceProvider::class];
    }

    /**
     * Load config test.
     *
     * @test
     */
    public function testLoadConfig()
    {
        $config = Config::get('laravel-multilingual');

        $this->assertTrue(is_array($config));
        $this->assertArrayHasKey('locales', $config);
        $this->assertArrayHasKey('translation_loaders', $config);
        $this->assertArrayHasKey('translation_manager', $config);
        $this->assertArrayHasKey('model', $config);
    }
}
