<?php

namespace mindtwo\LaravelMultilingual\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use mindtwo\LaravelMultilingual\Exceptions\Exception;
use mindtwo\LaravelMultilingual\Exceptions\TranslatableAttributeNotDefinedException;
use mindtwo\LaravelMultilingual\Exceptions\TranslatableAttributeTypeNotDefinedException;
use mindtwo\LaravelMultilingual\Services\Locale;

trait Translatable
{
    /**
     * Translatable key/value storage for relation sync after saving.
     *
     * @var array
     */
    private $translationValues = [];

    /**
     * The "booting" method of the trait.
     */
    public static function bootTranslatable()
    {
        static::saved(function (Model $model) {
            foreach ($model->translationValues as $attributeName=>$values) {
                $model->storeAttributeTranslations($attributeName, $values);
            }
        });

        static::registerSetMutator('translations', 'setAttributeTranslation');
        static::registerGetMutator('translations', 'getAttributeTranslation');
    }

    /**
     * Determinate if a translatable attribute is defined.
     *
     * @param string $attribute
     *
     * @return bool
     */
    public function hasTranslatableAttribute(string $attribute): bool
    {
        return isset($this->translations[$attribute]);
    }

    /**
     * Get the translatable attribute name or throw an exception, if it is not defined.
     *
     * @param $attribute
     *
     * @throws TranslatableAttributeNotDefinedException
     *
     * @return string
     */
    public function translatableAttributeOrFail($attribute): string
    {
        if (!$this->hasTranslatableAttribute($attribute)) {
            throw new TranslatableAttributeNotDefinedException(sprintf('Translatable attribute "%s" not defined', $attribute));
        }

        return $attribute;
    }

    /**
     * Get the normalized translatable type.
     *
     * @param string $type
     *
     * @return string
     */
    public function translatableType(string $type): string
    {
        return Str::singular(strtolower($type));
    }

    /**
     * Determinate if a translatable type exists and throw an exception if not.
     *
     * @param string $type
     *
     * @throws TranslatableAttributeTypeNotDefinedException
     *
     * @return string
     */
    public function translatableTypeOrFail(string $type): string
    {
        if (!$this->translatableTypeExists($type)) {
            throw new TranslatableAttributeTypeNotDefinedException(sprintf('Attribute type "%s" not defined', $type));
        }

        return $this->translatableType($type);
    }

    /**
     * Get the normalized translatable type.
     *
     * @param string $attribute
     *
     * @throws TranslatableAttributeNotDefinedException
     *
     * @return string
     */
    public function translatableTypeByAttribute(string $attribute): string
    {
        $attribute = $this->translatableAttributeOrFail($attribute);

        return $this->translatableType($this->translations[$attribute]);
    }

    /**
     * Determinate if a translatable type exists.
     *
     * @param string $type
     *
     * @return bool
     */
    public function translatableTypeExists(string $type): bool
    {
        return class_exists(config('laravel-multilingual.model.'.$this->translatableType($type)));
    }

    /**
     * Get the translatable relation by attribute name.
     *
     * @param string $name
     *
     * @throws Exception
     *
     * @return MorphMany
     */
    public function translatableRelationByAttribute(string $name): MorphMany
    {
        return $this->translatableRelationByType(
            $this->translatableTypeByAttribute($name)
        );
    }

    /**
     * Get the translatable relation by type.
     *
     * @param string $type
     *
     * @throws TranslatableAttributeTypeNotDefinedException
     *
     * @return MorphMany
     */
    public function translatableRelationByType(string $type): MorphMany
    {
        $type = $this->translatableTypeOrFail($type);

        return $this->morphMany(config('laravel-multilingual.model.'.$type), 'linkable');
    }

    /**
     * Set attribute translation.
     *
     * @param string $name
     * @param $value
     * @param null $locale
     *
     * @throws TranslatableAttributeNotDefinedException
     *
     * @return $this
     */
    protected function setAttributeTranslation(string $name, $value, $config = null, $locale = null): self
    {
        $name = $this->translatableAttributeOrFail($name);

        if (is_null($locale)) {
            $locale = app(Locale::class)->current;
        }

        // Convert scalar values to array
        $value = !is_array($value) ? [$locale => $value] : $value;

        // Map values to translation attributes
        $this->translationValues[$name] = collect($value)->mapWithKeys(function ($value, $locale) {
            return [$locale => $value];
        })->toArray();

        return $this;
    }

    /**
     * Get attribute translation.
     *
     * @param string      $name
     * @param string|null $locale
     *
     * @throws Exception
     * @throws TranslatableAttributeNotDefinedException
     *
     * @return string
     */
    public function getAttributeTranslation(string $name, $config = null, string $locale = null): string
    {
        $name = $this->translatableAttributeOrFail($name);

        if (is_null($locale)) {
            $locale = app(Locale::class)->current;
        }

        // Load translations, if there are no values for the given attribute
        if (!array_key_exists($name, $this->translationValues)) {
            $this->loadAttributeTranslations($name);
        }

        return $this->translationValues[$name][$locale] ?? $this->translationValues[$name][app(Locale::class)->fallback] ?? '';
    }

    /**
     * @param string      $name
     * @param string|null $locale
     *
     * @throws Exception
     *
     * @return string
     */
    public function translate(string $name, string $locale = null)
    {
        $config = isset($this->translations[$name]) ? $this->translations[$name] : null;

        return $this->getAttributeTranslation($name, $config, $locale);
    }

    /**
     * @param string $attributeName
     * @param array  $values
     *
     * @throws Exception
     */
    public function storeAttributeTranslations(string $attributeName, array $values = [])
    {
        foreach ($values as $locale => $value) {
            $this->translatableRelationByAttribute($attributeName)->updateOrCreate(
                [
                    'key'    => $attributeName,
                    'locale' => $locale,
                ],
                ['value' => $value]
            );
        }
    }

    /**
     * @param string $attribute
     *
     * @throws Exception
     *
     * @return Translatable
     */
    public function loadAttributeTranslations(string $attribute): self
    {
        $translations = $this->translatableRelationByAttribute($attribute)->where('key', $attribute)->get();

        $this->translationValues[$attribute] = collect($translations)->mapWithKeys(function ($item) {
            return [$item->locale => $item->value];
        })->toArray();

        return $this;
    }

    /**
     * Reload the current model instance with fresh attributes from the database.
     *
     * @return $this
     */
    public function refresh()
    {
        $this->translationValues = [];

        return parent::refresh();
    }

    /**
     * Get translations as array.
     *
     * @param string|null $locale
     *
     * @return array
     */
    public function translationsToArray($locale = null): array
    {
        if (is_null($locale)) {
            $locale = app(Locale::class)->current;
        }

        return collect($this->translations)->map(function ($type, $key) use ($locale) {
            return $this->getAttributeTranslation($key, $locale);
        })->toArray();
    }

    /**
     * Get the model and translations as array.
     *
     * @param string|null $locale
     *
     * @return array
     */
    public function toArrayWithTranslations($locale = null): array
    {
        if (is_null($locale)) {
            $locale = app(Locale::class)->current;
        }

        return array_merge(
            $this->toArray(),
            $this->translationsToArray($locale)
        );
    }

    public function __call($name, $arguments)
    {
        if (preg_match('/^translatable([A-Z][a-z]{1,})$/', $name, $matches)) {
            if ($this->translatableTypeExists($matches[1])) {
                return $this->translatableRelationByType($matches[1]);
            }
        }

        return parent::__call($name, $arguments);
    }
}
