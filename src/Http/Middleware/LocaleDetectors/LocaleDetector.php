<?php

namespace mindtwo\LaravelMultilingual\Http\Middleware\LocaleDetectors;

use Illuminate\Http\Request;
use mindtwo\LaravelMultilingual\Services\Locale;

interface LocaleDetector
{
    /**
     * @param  Request  $request
     * @param  Locale  $locale
     * @return bool
     */
    public function match(Request $request, Locale $locale): bool;

    /**
     * @param  Request  $request
     * @param  Locale  $locale
     * @return string
     */
    public function get(Request $request, Locale $locale): string;
}
