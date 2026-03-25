<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends BaseModel
{
    use SoftDeletes;
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'meta_title',
        'meta_description',
        'thumbnail',
    ];

    /**
     * Auto-generate slug from name
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
                $count = static::where('slug', $category->slug)->count();
                if ($count > 0) {
                    $category->slug .= '-' . ($count + 1);
                }
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $newSlug = Str::slug($category->name);
                $count = static::where('slug', $newSlug)->where('id', '!=', $category->id)->count();
                $category->slug = $count > 0 ? $newSlug . '-' . ($count + 1) : $newSlug;
            }
        });
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
