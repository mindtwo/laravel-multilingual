<?php

namespace mindtwo\LaravelMultilingual\Models\Traits;

use Illuminate\Support\Facades\Cache;

trait TranslationCalls
{
    /**
     * Boot the translation calls trait for a model.
     */
    public static function bootTranslationCalls()
    {
        static::updated(function (self $model) {
            $model->flushGroupCache();
        });

        static::deleted(function (self $model) {
            $model->flushGroupCache();
        });
    }

    /**
     * Get translation based on a given group and key.
     *
     * @param string $locale
     * @param string $group
     *
     * @return array
     */
    public static function getTranslationsForGroup(string $locale, string $group): array
    {
        return Cache::rememberForever(static::getCacheKey($group, $locale), function () use ($group, $locale) {
            return static::query()
                ->where(function ($query) use ($group) {
                    $query->where('group', $group);
                    $query->orWhere('group', 'LIKE', $group.'.%');
                })
                ->where('locale', $locale)
                ->get()
                ->reduce(function ($lines, $model) use ($group) {
                    if (! is_null($model->value)) {
                        $group = collect(explode('.', $model->group))
                            ->slice(1) // Remove first entry for laravel compatibility. The first segment is the filename on the default file loader.
                            ->push($model->key) // Push translation key as a last segment
                            ->implode('.');

                        // Inverse of array_dot() function.
                        array_set($lines, $group, $model->value);
                    }

                    return $lines;
                }) ?? [];
        });
    }

    /**
     * Generate cache key.
     *
     * @param string $group
     * @param string $locale
     *
     * @return string
     */
    public static function getCacheKey(string $group, string $locale): string
    {
        $classNameArray = explode('\\', __CLASS__);
        $className = last($classNameArray);

        return strtolower("translations.{$className}.{$group}.{$locale}");
    }

    /**
     * Flush translation cache.
     *
     * @return bool
     */
    protected function flushGroupCache()
    {
        return Cache::forget(static::getCacheKey($this->group ?? '', $this->locale));
    }
}
