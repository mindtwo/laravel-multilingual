<?php

namespace mindtwo\LaravelMultilingual\Listeners;

use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Support\Facades\App;
use mindtwo\LaravelMultilingual\Services\Locale;

class LocaleChangedEventSubscriber
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return string
     */
    public static function changeLocale($event): string
    {
        App::make(Locale::class)->setCurrentLocaleAttribute($event->locale);

        return $event->locale;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param object $events
     */
    public function subscribe($events)
    {
        $events->listen(LocaleUpdated::class, [LocaleChangedEventSubscriber::class, 'changeLocale']);
    }
}
