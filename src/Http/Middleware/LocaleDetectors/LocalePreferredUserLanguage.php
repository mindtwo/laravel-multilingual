<?php

namespace mindtwo\LaravelMultilingual\Http\Middleware\LocaleDetectors;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mindtwo\LaravelMultilingual\Services\Locale;

class LocalePreferredUserLanguage implements LocaleDetector
{
    /**
     * @param Request $request
     * @param Locale  $locale
     *
     * @return bool
     */
    public function match(Request $request, Locale $locale): bool
    {
        return Auth::user() && is_callable([Auth::user(), 'preferredLocale']);
    }

    /**
     * @param Request $request
     * @param Locale  $locale
     *
     * @return string
     */
    public function get(Request $request, Locale $locale): string
    {
        return Auth::user()->preferredLocale();
    }
}
