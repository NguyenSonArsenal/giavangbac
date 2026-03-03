<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends BaseModel
{
    protected $table = 'category';
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
