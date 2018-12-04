<?php

namespace mindtwo\LaravelMultilingual\Http\Middleware\LocaleDetectors;

use Illuminate\Http\Request;
use mindtwo\LaravelMultilingual\Services\Locale;

class LocaleInQueryString implements LocaleDetector
{
    /**
     * @param Request $request
     * @param Locale  $locale
     *
     * @return bool
     */
    public function match(Request $request, Locale $locale): bool
    {
        return $locale->available->contains($request->input('locale'));
    }

    /**
     * @param Request $request
     * @param Locale  $locale
     *
     * @return string
     */
    public function get(Request $request, Locale $locale): string
    {
        return $request->input('locale');
    }
}
