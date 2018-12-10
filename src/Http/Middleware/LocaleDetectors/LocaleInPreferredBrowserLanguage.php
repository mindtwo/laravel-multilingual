<?php

namespace mindtwo\LaravelMultilingual\Http\Middleware\LocaleDetectors;

use Illuminate\Http\Request;
use mindtwo\LaravelMultilingual\Services\Locale;

class LocaleInPreferredBrowserLanguage implements LocaleDetector
{
    /**
     * @param Request $request
     * @param Locale  $locale
     *
     * @return bool
     */
    public function match(Request $request, Locale $locale): bool
    {
        return !empty($this->detectPreferredLanguage());
    }

    /**
     * @param Request $request
     * @param Locale  $locale
     *
     * @return string
     */
    public function get(Request $request, Locale $locale): string
    {
        return $this->detectPreferredLanguage();
    }

    /**
     * @param Request $request
     * @param Locale  $locale
     *
     * @return string
     */
    public function detectPreferredLanguage(Request $request, Locale $locale): string
    {
        return $request->getPreferredLanguage($locale->available->toArray());
    }
}
