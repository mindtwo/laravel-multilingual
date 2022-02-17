<?php

namespace mindtwo\LaravelMultilingual\Tests\Unit\Services;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use mindtwo\LaravelMultilingual\Models\ContentTypeString;
use mindtwo\LaravelMultilingual\Models\ContentTypeText;
use mindtwo\LaravelMultilingual\Providers\EventServiceProvider;
use mindtwo\LaravelMultilingual\Providers\MultilingualServiceProvider;
use mindtwo\LaravelMultilingual\Providers\TranslationServiceProvider;
use mindtwo\LaravelMultilingual\Tests\TestCase;

class TranslationLoaderManagerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
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
     * Create a translation.
     *
     * @param $value
     * @param  string  $key
     * @param  string  $group
     * @param  string  $locale
     * @param  string  $type
     * @return ContentTypeString|ContentTypeText
     */
    protected static function createTrans($value, string $key, string $group, string $locale = 'en', string $type = ContentTypeString::class)
    {
        return $type::create([
            'value'  => $value,
            'key'    => $key,
            'group'  => $group,
            'locale' => $locale,
        ]);
    }

    /**
     * Get string translation test.
     *
     * @test
     */
    public function testGetStringTranslation()
    {
        self::createTrans('test-value', 'test', 'test', 'de');

        $this->assertEquals('test-value', trans('test.test', [], 'de'));
    }

    /**
     * Get fallback string translation test.
     *
     * @test
     */
    public function testGetFallbackStringTranslation()
    {
        self::createTrans('test-value', 'test', 'test');

        $this->assertEquals('test-value', trans('test.test', [], 'de'));
    }

    /**
     * Get text translation test.
     *
     * @test
     */
    public function testGetTextTranslation()
    {
        self::createTrans('test-value', 'test', 'test', 'de', ContentTypeText::class);

        $this->assertEquals('test-value', trans('test.test', [], 'de'));
    }

    /**
     * Get fallback text translation test.
     *
     * @test
     */
    public function testGetFallbackTextTranslation()
    {
        self::createTrans('test-value', 'test', 'test', 'en', ContentTypeText::class);

        $this->assertEquals('test-value', trans('test.test', [], 'de'));
    }

    /**
     * Get translation strings before texts test.
     *
     * @test
     */
    public function testGetTranslationStringsBeforeTexts()
    {
        self::createTrans('test-string', 'test', 'test');
        self::createTrans('test-text', 'test', 'test', 'en', ContentTypeText::class);

        $this->assertEquals('test-string', trans('test.test'));
    }

    /**
     * Get sub grouped translations test.
     *
     * @test
     */
    public function testGetSubGroupedTranslations()
    {
        self::createTrans('test-string', 'test', 'nested.groups');

        $this->assertEquals('test-string', trans('nested.groups.test'));
        $this->assertArrayHasKey('test', trans('nested.groups'));
    }
}
