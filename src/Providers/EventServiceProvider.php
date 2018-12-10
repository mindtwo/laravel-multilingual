<?php

namespace mindtwo\LaravelMultilingual\Providers;

use mindtwo\LaravelMultilingual\Services\Locale;
use mindtwo\LaravelMultilingual\Listeners\LocaleChangedEventSubscriber;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as IlluminateEventServiceProvider;

class EventServiceProvider extends IlluminateEventServiceProvider
{
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        LocaleChangedEventSubscriber::class,
    ];

    /**
     * Register any other events for your application.
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(Locale::class);
    }
}
