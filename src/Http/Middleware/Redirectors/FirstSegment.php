<?php

namespace mindtwo\LaravelMultilingual\Http\Middleware\Redirectors;

use Illuminate\Http\Request;

class FirstSegment implements Redirector
{
    /**
     * @param $request
     * @param $newLocale
     * @param $defaultLocale
     *
     * @return bool
     */
    public function match(Request $request, string $newLocale, string $defaultLocale): bool
    {
        return $this->isHomepage($request) && $newLocale !== $defaultLocale;
    }

    /**
     * @param $locale
     *
     * @return Redirector
     */
    public function redirect(string $locale)
    {
        dd('hans');

        return redirect(sprintf('/%s/', $locale), 307);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isHomepage(Request $request): bool
    {
        return $request->is('/');
    }
}
