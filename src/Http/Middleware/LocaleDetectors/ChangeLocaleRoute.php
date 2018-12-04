<?php

namespace mindtwo\LaravelMultilingual\Http\Middleware\LocaleDetectors;

use Illuminate\Http\Request;
use mindtwo\LaravelMultilingual\Services\Locale;

class ChangeLocaleRoute implements LocaleDetector
{
    /**
     * @param Request $request
     * @param Locale  $locale
     *
     * @return bool
     */
    public function match(Request $request, Locale $locale): bool
    {
        return $request->is('locale/*');
    }

    /**
     * @param Request $request
     * @param Locale  $locale
     *
     * @return string
     */
    public function get(Request $request, Locale $locale): string
    {
        return last($request->segments());
    }
}
