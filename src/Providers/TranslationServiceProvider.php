<?php

namespace mindtwo\LaravelMultilingual\Providers;

use Illuminate\Translation\TranslationServiceProvider as IlluminateTranslationServiceProvider;

class TranslationServiceProvider extends IlluminateTranslationServiceProvider
{
    /**
     * Register the translation line loader. This method registers a
     * `TranslationLoaderManager` instead of a simple `FileLoader` as the
     * applications `translation.loader` instance.
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            $class = $this->app['config']->get('laravel-multilingual.translation_manager');

            return new $class($app['files'], $app['path.lang']);
        });
    }
}
