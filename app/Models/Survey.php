<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Survey extends Model
{
    use HasFactory, HasSlug;

    const TYPE_TEXT = 'text';
    const TYPE_SELECT = 'select';
    const TYPE_RADIO = 'radio';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_TEXTAREA = 'textarea';

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    protected $fillable = [
        'user_id',
        'title',
        'image',
        'description',
        'status',
        'expire_date'
    ];

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class);
    }

    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class);
    }
}
