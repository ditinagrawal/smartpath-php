<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webinar extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'event_date',
        'event_time',
        'location',
        'registration_link',
        'is_published',
        'image'
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_published' => 'boolean',
    ];

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('public/storage/' . $this->image);
        }
        return null;
    }
}
