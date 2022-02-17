<?php

namespace mindtwo\LaravelMultilingual\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use mindtwo\LaravelMultilingual\Services\Locale;

class Localization
{
    /**
     * @var Locale
     */
    protected $locale;

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @param  string|null  $guard
     * @return mixed
     *
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $this->locale = app(config('laravel-multilingual.locale_class'));

        // Detect and save locale
        $newLocale = $this->detectAndSetLocale($request);

        // Create locale cookie
        $localeCookie = cookie()->forever('locale', $newLocale);

        // Redirect with cookie
        foreach (config('laravel-multilingual.locale_redirectors') as $redirector) {
            $redirector = app($redirector);
            if ($redirector->match($request, $newLocale, $this->locale->current)) {
                dd('test');

                return $redirector->redirect($newLocale)->withCookie($localeCookie);
            }
        }

        // Proceed with cookie
        return $next($request)->withCookie($localeCookie);
    }

    /**
     * @param $request
     * @return string locale
     *
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function detectAndSetLocale($request): string
    {
        $locale = $this->getRequestedLocale($request);

        app()->setLocale($locale);

        return $locale;
    }

    /**
     * @param  Request  $request
     * @return string
     *
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    protected function getRequestedLocale(Request $request): string
    {
        foreach (config('laravel-multilingual.locale_detectors') as $detector) {
            $detector = app($detector);
            if ($detector->match($request, $this->locale)) {
                return $detector->get($request, $this->locale);
            }
        }

        return $this->locale->default;
    }
}
