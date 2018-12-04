<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Available Locales
    |--------------------------------------------------------------------------
    |
    | This option controls the available locales for your application. You may
    | change these defaults as required.
    |
    */
    'locales' => [],

    /*
    |--------------------------------------------------------------------------
    | Locale Class
    |--------------------------------------------------------------------------
    |
    | You can put any class here that implements extends the
    | mindtwo\LaravelMultilingual\Services class.
    |
    */
    'locale_class' => mindtwo\LaravelMultilingual\Services\Locale::class,

    /*
    |--------------------------------------------------------------------------
    | Translation Loaders
    |--------------------------------------------------------------------------
    |
    | Translation Loader Manager will be fetched by these loaders.
    | You can put any class here that implements
    | the mindtwo\LaravelMultilingual\Services\TranslationLoader interface.
    |
    */
    'translation_loaders' => [
        mindtwo\LaravelMultilingual\Services\TranslationLoaderDb::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation Model
    |--------------------------------------------------------------------------
    |
    | This is the model used by the Db Translation loader. You can put any
    | model here that uses the following trait:
    | mindtwo\LaravelMultilingual\Models\Traits\TranslationCalls.
    |
    */
    'model' => [
        'string' => mindtwo\LaravelMultilingual\Models\ContentTypeString::class,
        'text'   => mindtwo\LaravelMultilingual\Models\ContentTypeText::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation Manager Class
    |--------------------------------------------------------------------------
    |
    | This is the translation manager which overrides the default Laravel `translation.loader`
    |
    */
    'translation_manager' => mindtwo\LaravelMultilingual\Services\TranslationLoaderManager::class,

    /*
    |--------------------------------------------------------------------------
    | Locale Detectors
    |--------------------------------------------------------------------------
    |
    | The Localization middleware will be fetched by these detectors.
    | You can put any class here that implements
    | the mindtwo\LaravelMultilingual\Http\Middleware\LocaleDetectors interface.
    |
    */
    'locale_detectors' => [
        mindtwo\LaravelMultilingual\Http\Middleware\LocaleDetectors\ChangeLocaleRoute::class,
        mindtwo\LaravelMultilingual\Http\Middleware\LocaleDetectors\LocaleInFirstSegment::class,
        mindtwo\LaravelMultilingual\Http\Middleware\LocaleDetectors\LocaleInQueryString::class,
        mindtwo\LaravelMultilingual\Http\Middleware\LocaleDetectors\LocalePreferredUserLanguage::class,
        mindtwo\LaravelMultilingual\Http\Middleware\LocaleDetectors\LocaleInCookie::class,
        mindtwo\LaravelMultilingual\Http\Middleware\LocaleDetectors\LocaleInPreferredBrowserLanguage::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Locale middleware redirectors
    |--------------------------------------------------------------------------
    |
    | The redirectors will be fetched by the localization middleware.
    | You can put any class here that implements the
    | mindtwo\LaravelMultilingual\Http\Middleware\Redirectors\Redirector interface.
    |
    */
    'locale_redirectors' => [
        mindtwo\LaravelMultilingual\Http\Middleware\Redirectors\FirstSegment::class,
    ],
];
