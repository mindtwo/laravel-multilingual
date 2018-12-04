<?php

namespace mindtwo\LaravelMultilingual\Tests\Unit\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use mindtwo\LaravelMultilingual\Http\Middleware\Localization;
use mindtwo\LaravelMultilingual\Providers\EventServiceProvider;
use mindtwo\LaravelMultilingual\Providers\MultilingualServiceProvider;
use mindtwo\LaravelMultilingual\Providers\TranslationServiceProvider;
use mindtwo\LaravelMultilingual\Tests\TestCase;

class LocalizationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:KttAWPgdFefhCUAtb3eMBrQkd3mTCInarN6s9dzJCmM=');
        $app['config']->set('app.locale', 'en');
        $app['config']->set('laravel-multilingual.locales', ['en', 'de']);
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

    protected function getUrl($url)
    {
        Route::get($url, function () { return true; })->middleware([
            EncryptCookies::class,
            StartSession::class,
            Localization::class,
        ]);

        return $this->get($url);
    }

    public function testItSavesACorrectCookie()
    {
        $response = $this->getUrl('/locale/de');
        $response->assertCookie('locale', 'de');

        $response = $this->getUrl('/locale/en');
        $response->assertCookie('locale', 'en');
    }
    

    /*

    public function testItRedirectsBackToTheHomepage()
    {
        $this->createSiteLanguage('en');
        $this->createSiteLanguage('de');

        Route::get('/de/', function () { return 'DE'; });

        $response = $this->call('GET', '/locale/de');
        $response->assertRedirect('/de');
    }

    public function testItUsesBrowserBasedLocaleInfo()
    {
        $this->createSiteLanguage(config('app.locale'));
        $this->createSiteLanguage('de');

        $response = $this->call('GET', '/', [], [], [], ['HTTP_Accept-language' => 'de, en; q=0.8, en; q=0.6']);
        $response->assertRedirect('/de');

        $response = $this->call('GET', '/', [], [], [], ['HTTP_Accept-language' => random_bytes(100)]);
        $response->assertCookie('locale', config('app.locale'));
    }

    public function testItPrioritizesLocaleChangeUrlFirst()
    {
        $this->createSiteLanguage('de');

        $response = $this->call('GET', '/locale/de', [], [], [], ['HTTP_Accept-language' => 'en']);
        $response->assertCookie('locale', 'de');

        $response = $this->call('GET', '/locale/de?locale=en');
        $response->assertCookie('locale', 'de');
    }

    public function testItPrioritizesLocaleGetParameterThird()
    {
        $this->createSiteLanguage(config('app.locale'));
        $this->createSiteLanguage('de');

        $response = $this->call('GET', '/?locale=de', [], [], [], ['HTTP_Accept-language' => 'en']);
        $response->assertCookie('locale', 'de');

        $response = $this->call('GET', '/?locale=de', [], ['locale' => 'en'], [], ['HTTP_Accept-language' => 'en']);
        $response->assertCookie('locale', 'de');
    }
    */
}
