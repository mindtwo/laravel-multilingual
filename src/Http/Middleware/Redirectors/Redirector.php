<?php

namespace mindtwo\LaravelMultilingual\Http\Middleware\Redirectors;

use Illuminate\Http\Request;

interface Redirector
{
    /**
     * @param $request
     * @param $newLocale
     * @param $defaultLocale
     *
     * @return bool
     */
    public function match(Request $request, string $newLocale, string $defaultLocale): bool;

    /**
     * @param $locale
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function redirect(string $locale);
}
