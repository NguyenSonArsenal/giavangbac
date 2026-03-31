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
        'excerpt',
        'content',
        'thumbnail',
        'category_id',
        'meta_title',
        'meta_description',
        'view_count',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'is_featured'  => 'boolean',
        'view_count'   => 'integer',
        'status'       => 'integer',
    ];

    /**
     * Auto-generate slug from title
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope: chỉ lấy bài active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope: bài nổi bật
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
