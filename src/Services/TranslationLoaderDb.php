<?php

namespace mindtwo\LaravelMultilingual\Services;

class TranslationLoaderDb implements TranslationLoader
{
    /**
     * @param string $locale
     * @param string $group
     *
     * @return array
     */
    public function loadTranslations(string $locale, string $group): array
    {
        foreach ($this->getConfiguredModelClass() as $model) {
            $translation = $model::getTranslationsForGroup($locale, $group);
            if (! empty($translation)) {
                return $translation;
            }
        }
        return [];
    }

    /**
     * @return array
     */
    protected function getConfiguredModelClass(): array
    {
        $modelClassOrClasses = config('laravel-multilingual.model');

        return is_array($modelClassOrClasses) ? $modelClassOrClasses : [$modelClassOrClasses];
    }
}
