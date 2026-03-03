<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends BaseModel
{
    protected $table = 'product';

    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'price', 'sale', 'price_sale', 'content', 'category_id', 'image', 'status',
    ];

    /**
    * Quan hệ với Category
    * Một sản phẩm thuộc về một category
    */
    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /** * Quan hệ: Product có nhiều ảnh phụ */
    public function images() {
        return $this->hasMany(ProductImage::class)->orderBy('sort');
    }

    /** * Quan hệ: Product có nhiều variant (đóng gói) */
    public function variants() {
        return $this->hasMany(ProductVariant::class);
    }

    /** * Ví dụ helper: lấy giá hiển thị */
    public function getDisplayPriceAttribute() {
        return $this->price_sale > 0 ? $this->price_sale : $this->price;
    }
}
