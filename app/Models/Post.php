<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends BaseModel
{
    use SoftDeletes;

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'des',
        'content',
    ];

    /**
     * Auto-generate slug from title on create
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
                // Ensure unique slug
                $count = static::where('slug', $post->slug)->count();
                if ($count > 0) {
                    $post->slug .= '-' . ($count + 1);
                }
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && !$post->isDirty('slug')) {
                $newSlug = Str::slug($post->title);
                $count = static::where('slug', $newSlug)->where('id', '!=', $post->id)->count();
                $post->slug = $count > 0 ? $newSlug . '-' . ($count + 1) : $newSlug;
            }
        });
    }
}
