<?php

namespace mindtwo\LaravelMultilingual\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use mindtwo\LaravelMultilingual\Models\Traits\TranslationCalls;

class ContentTypeText extends Model
{
    use SoftDeletes,
        TranslationCalls;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'locale',
        'group',
        'key',
        'value',
    ];

    /**
     * Get all of the owning linkable models.
     */
    public function linkable()
    {
        return $this->morphTo();
    }
}
