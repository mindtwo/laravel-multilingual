<?php

namespace mindtwo\LaravelMultilingual\Http\Middleware\LocaleDetectors;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use mindtwo\LaravelMultilingual\Services\Locale;

class LocaleInCookie implements LocaleDetector
{
    /**
     * @param  Request  $request
     * @param  Locale  $locale
     * @return bool
     */
    public function match(Request $request, Locale $locale): bool
    {
        return $request->hasCookie('locale');
    }

    /**
     * @param  Request  $request
     * @param  Locale  $locale
     * @return string
     */
    public function get(Request $request, Locale $locale): string
    {
        $locale = $request->cookie('locale');

        return strlen($locale) > 5 ? Crypt::decrypt($locale) : $locale;
    }
}
