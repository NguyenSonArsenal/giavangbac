<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImage extends BaseModel
{
    protected $table = 'product_image';
    use SoftDeletes;
    protected $fillable = [
        'product_id', 'path', 'sort'
    ];

    /** * Quan hệ: ảnh phụ thuộc về sản phẩm */
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
