<?php

namespace mindtwo\LaravelMultilingual\Tests\Unit\Traits;

use Illuminate\Database\Eloquent\Model;
use mindtwo\LaravelMultilingual\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use mindtwo\LaravelMultilingual\Models\Traits\Translatable;
use Mindtwo\DynamicMutators\Traits\HasDynamicMutators;
use mindtwo\LaravelMultilingual\Providers\EventServiceProvider;
use mindtwo\LaravelMultilingual\Providers\TranslationServiceProvider;
use mindtwo\LaravelMultilingual\Providers\MultilingualServiceProvider;

class TranslatableTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            EventServiceProvider::class,
            MultilingualServiceProvider::class,
            TranslationServiceProvider::class,
        ];
    }

    /**
     * Return a mocked translatable object.
     *
     * @return Model
     */
    protected function mockLinkableClass(): Model
    {
        return new class() extends Model {
            use HasDynamicMutators,
                Translatable;

            protected $translations = [
                'label'       => 'string',
                'description' => 'text',
            ];

            protected $fillable = [
                'label',
                'description',
            ];

            public function save(array $options = [])
            {
                $this->fireModelEvent('saved');
            }

            public function getIdAttribute()
            {
                return 1;
            }
        };
    }

    /**
     * Set translated attribute in current language.
     *
     * @test
     */
    public function testSetTranslatedAttributeInCurrentLanguage()
    {
        $model = $this->mockLinkableClass();

        $model->label = 'Example';
        $model->description = 'Lorem ipsum';

        $this->assertEquals('Example', $model->label);
        $this->assertEquals('Lorem ipsum', $model->description);

        $model->save();
        $model->refresh();

        $this->assertEquals('Example', $model->label);
        $this->assertEquals('Lorem ipsum', $model->description);
    }

    /**
     * Set translated attribute in multiple languages.
     *
     * @test
     */
    public function testSetTranslatedAttributeInMultipleLanguages()
    {
        $model = $this->mockLinkableClass();

        $model->label = [
            'en' => 'Example',
            'de' => 'Beispiel',
        ];

        $this->assertEquals('Example', $model->label);
        $this->assertEquals('Beispiel', $model->translate('label', 'de'));

        $model->save();
        $model->refresh();

        $this->assertEquals('Example', $model->label);
        $this->assertEquals('Beispiel', $model->translate('label', 'de'));
    }

    /**
     * Update existing translations.
     *
     * @test
     */
    public function testUpdateExistingTranslations()
    {
        $model = $this->mockLinkableClass()->create([
            'label' => [
                'en' => 'Example',
                'de' => 'Beispiel',
            ],
        ]);

        $model->fill([
            'label' => [
                'en' => 'Updated example',
                'de' => 'Aktualisiertes Beispiel',
            ],
        ]);

        $this->assertEquals('Updated example', $model->label);
        $this->assertEquals('Aktualisiertes Beispiel', $model->translate('label', 'de'));

        $model->save();
        $model->refresh();

        $this->assertEquals('Updated example', $model->label);
        $this->assertEquals('Aktualisiertes Beispiel', $model->translate('label', 'de'));
    }

    /**
     * Access magic string relation test.
     *
     * @test
     */
    public function testAccessMagicStringRelation()
    {
        $model = $this->mockLinkableClass()->create([
            'label' => 'Example',
        ]);

        $strings = $model->translatableStrings()->get();

        $this->assertCount(1, $strings);
    }

    /**
     * Access magic text relation test.
     *
     * @test
     */
    public function testAccessMagicTextRelation()
    {
        $model = $this->mockLinkableClass()->create([
            'description' => 'Example',
        ]);

        $strings = $model->translatableTexts()->get();

        $this->assertCount(1, $strings);
    }
}
