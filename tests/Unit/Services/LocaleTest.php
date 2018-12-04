<?php

namespace mindtwo\LaravelMultilingual\Tests\Unit\Services;

use Illuminate\Support\Facades\App;
use mindtwo\LaravelMultilingual\Exceptions\LocaleNotAvailableException;
use mindtwo\LaravelMultilingual\Exceptions\LocaleServiceNotAvailableException;
use mindtwo\LaravelMultilingual\Providers\EventServiceProvider;
use mindtwo\LaravelMultilingual\Providers\MultilingualServiceProvider;
use mindtwo\LaravelMultilingual\Providers\TranslationServiceProvider;
use mindtwo\LaravelMultilingual\Services\Locale;
use mindtwo\LaravelMultilingual\Tests\TestCase;

class LocaleTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.locale', 'en');
        $app['config']->set('laravel-multilingual.locales', [
            'en',
            'de',
            'fr',
            'backend' => [
                'en',
            ],
            'frontend' => [
                'de',
                'it',
            ],
        ]);
    }

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
            EventServiceProvider::class,
            MultilingualServiceProvider::class,
            TranslationServiceProvider::class,
        ];
    }

    /**
     * Get available locales test.
     *
     * @test
     */
    public function testGetAvailableLocales()
    {
        $locales = App::make(Locale::class)->available;

        $this->assertContains('en', $locales);
        $this->assertContains('de', $locales);
        $this->assertContains('fr', $locales);
        $this->assertContains('it', $locales);
    }

    /**
     * Get available locales in Service test.
     *
     * @test
     */
    public function testGetAvailableLocalesInService()
    {
        $locales = App::make(Locale::class)->availableInService('frontend');

        $this->assertContains('de', $locales);
        $this->assertContains('it', $locales);
    }

    /**
     * Get available locales in service throws an exception, if service is not defined test.
     *
     * @test
     */
    public function testGetAvailableLocalesInServiceThrowsAnExceptionIfServiceIsNotDefined()
    {
        $this->expectException(LocaleServiceNotAvailableException::class);

        App::make(Locale::class)->availableInService('undefined-service');
    }

    /**
     * Get current locale test.
     *
     * @test
     */
    public function testGetCurrentLocale()
    {
        $this->assertEquals('en', App::make(Locale::class)->current);
    }

    /**
     * Get current locale after locale change test.
     *
     * @test
     */
    public function testGetCurrentLocaleAfterLocaleChange()
    {
        App::setLocale('de');

        $this->assertEquals('de', App::make(Locale::class)->current);
    }

    /**
     * Get current locale after locale change on locale class test.
     *
     * @test
     */
    public function testGetCurrentLocaleAfterLocaleChangeOnLocaleClass()
    {
        App::make(Locale::class)->setCurrentLocaleAttribute('de');

        $this->assertEquals('de', App::getLocale());
    }

    /**
     * Set same locale test.
     *
     * @test
     */
    public function testSetSameLocale()
    {
        App::setLocale('en');

        $this->assertEquals('en', App::getLocale());
    }

    /**
     * Throws an exception, if locale is not available test.
     *
     * @test
     */
    public function testThrowsAnExceptionIfLocaleIsNotAvailable()
    {
        $this->expectException(LocaleNotAvailableException::class);

        App::setLocale('unavailable');
    }

    /**
     * Get locale test.
     *
     * @test
     */
    public function testGetLocale()
    {
        $this->assertEquals('en', App::make(Locale::class)->getOrFail('en'));
    }

    /**
     * Get locale throws an exception, if locale is unavailable test.
     *
     * @test
     */
    public function testGetLocaleThrowsAnExceptionIfLocaleIsUnavailable()
    {
        $this->expectException(LocaleNotAvailableException::class);

        App::make(Locale::class)->getOrFail('unavailable-locale');
    }

    /**
     * Locale is available test.
     *
     * @test
     */
    public function testLocaleIsAvailable()
    {
        $locale = App::make(Locale::class);

        $this->assertTrue($locale->isAvailable('en'));
        $this->assertTrue($locale->isAvailable('en', 'backend'));
        $this->assertFalse($locale->isAvailable('unavailable-locale'));
        $this->assertFalse($locale->isAvailable('unavailable-locale', 'backend'));
    }
}
