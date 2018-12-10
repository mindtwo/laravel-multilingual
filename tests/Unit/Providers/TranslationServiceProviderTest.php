<?php

namespace mindtwo\LaravelMultilingual\Tests\Unit\Providers;

use Illuminate\Support\Facades\App;
use mindtwo\LaravelMultilingual\Tests\TestCase;
use mindtwo\LaravelMultilingual\Providers\TranslationServiceProvider;
use mindtwo\LaravelMultilingual\Providers\MultilingualServiceProvider;

class TranslationServiceProviderTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            MultilingualServiceProvider::class,
            TranslationServiceProvider::class,
        ];
    }

    /**
     * Translation manager is registered test.
     *
     * @test
     */
    public function testTranslationManagerIsRegistered()
    {
        $loader = App::make('translation.loader');

        $this->assertInstanceOf(config('laravel-multilingual.translation_manager'), $loader);
    }
}
