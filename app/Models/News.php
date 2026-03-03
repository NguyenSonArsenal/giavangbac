<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends BaseModel
{
    use SoftDeletes;

    protected $table = 'new';
    protected $fillable = [
        'title',
        'slug',
        'des',
        'content',
    ];

    public function getReadingTimeAttribute()
    {
        $chars = strlen(strip_tags($this->content)); // hoặc field mô tả/bài viết
        return ceil($chars / 200);
    }
}
