<?php

namespace mindtwo\LaravelMultilingual\Services;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Config\Repository as Config;
use mindtwo\LaravelMultilingual\Exceptions\LocaleNotAvailableException;
use mindtwo\LaravelMultilingual\Exceptions\LocaleServiceNotAvailableException;

class Locale extends Collection
{
    /**
     * Configuration.
     *
     * @var Config
     */
    protected $config;

    /**
     * Locale constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        parent::__construct([
            'default' => $this->config->get('app.locale'),
        ]);
    }

    /**
     * Get the fallback locale.
     *
     * @return string
     */
    protected function getFallbackAttribute(): string
    {
        return $this->config->get('app.fallback_locale');
    }

    /**
     * Get the current language.
     *
     * @throws Exception
     *
     * @return string
     */
    protected function getCurrentAttribute(): string
    {
        return $this->config->get('app.locale');
    }

    /**
     * Get a list of available site languages.
     *
     * @return Collection
     */
    protected function getAvailableAttribute(): Collection
    {
        return $this->available();
    }

    /**
     * Get a list of available site languages.
     *
     * @return Collection
     */
    public function available(): Collection
    {
        if (! array_key_exists('available', $this->items)) {
            $locales = collect($this->config->get('laravel-multilingual.locales'));

            $this->items['available'] = $locales->flatten()->unique();
        }

        return $this->items['available'];
    }

    /**
     * Get a list of available locales within a service.
     *
     * @param string $service
     *
     * @throws LocaleServiceNotAvailableException
     *
     * @return Collection
     */
    public function availableInService(string $service): Collection
    {
        $service = collect($this->config->get('laravel-multilingual.locales'))->only($service)->flatten();

        if ($service->isEmpty()) {
            throw new LocaleServiceNotAvailableException();
        }

        return $service->mapWithKeys(function ($locale) {
            return [$locale => $locale];
        });
    }

    /**
     * Set current locale.
     *
     * @param string $locale
     *
     * @throws LocaleNotAvailableException
     * @throws LocaleServiceNotAvailableException
     *
     * @return Locale
     */
    public function setCurrentLocaleAttribute(string $locale): self
    {
        if (! $this->isAvailable($locale)) {
            throw new LocaleNotAvailableException();
        }

        if (app()->getLocale() !== $locale) {
            app()->setLocale($locale);
        }

        $this->items['current'] = $locale;

        return $this;
    }

    /**
     * Get a locale or throw an exception, if it is unavailable.
     *
     * @param string      $locale
     * @param string|null $service
     *
     * @throws LocaleNotAvailableException
     * @throws LocaleServiceNotAvailableException
     *
     * @return string
     */
    public function getOrFail(string $locale, string $service = null): string
    {
        if (! $this->isAvailable($locale, $service)) {
            throw new LocaleNotAvailableException();
        }

        return $locale;
    }

    /**
     * Determinate if a locale is available.
     *
     * @param string      $locale
     * @param string|null $service
     *
     * @throws LocaleServiceNotAvailableException
     *
     * @return bool
     */
    public function isAvailable(string $locale, string $service = null): bool
    {
        if (is_null($service)) {
            return $this->available->contains($locale);
        }

        return $this->availableInService($service)->contains($locale);
    }

    /**
     * Get the value of an attribute using its mutator.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function mutateAttribute($key)
    {
        return $this->{'get'.Str::studly($key).'Attribute'}();
    }

    /**
     * Get an element from the collection.
     *
     * @param string $key
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function __get($key)
    {
        if (method_exists($this, 'get'.Str::studly($key).'Attribute')) {
            if (array_key_exists($key, $this->items)) {
                return $this->items[$key];
            }

            return $this->items[$key] = $this->mutateAttribute($key);
        }

        if ($this->has($key)) {
            return $this->items[$key];
        }

        return parent::__get($key);
    }
}
