<?php

namespace mindtwo\LaravelMultilingual\Tests\Unit\Providers;

use Illuminate\Support\Facades\App;
use mindtwo\LaravelMultilingual\Providers\EventServiceProvider;
use mindtwo\LaravelMultilingual\Services\Locale;
use mindtwo\LaravelMultilingual\Tests\TestCase;
use Mockery;

class EventServiceProviderTest extends TestCase
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
        return [EventServiceProvider::class];
    }

    /**
     * Clean up the testing environment before the next test.
     */
    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Locale change event is triggered test.
     *
     * @test
     */
    public function testLocaleChangeEventIsTriggered()
    {
        // Use spied locale class
        App::extend(Locale::class, function () {
            return Mockery::spy(Locale::class);
        });

        // Change locale
        App::setLocale('de');

        // Assert event callback method was called
        $result = App::make(Locale::class)->shouldHaveReceived('setCurrentLocaleAttribute', ['de'])->once();
        $this->assertNotEmpty($result);
    }
}
