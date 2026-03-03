<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_variant';

    protected $fillable = [
        'product_id',
        'unit',
        'quantity_per_unit',
        'price',
        'price_sale',
        'sale',
        'stock',
        'sku',
        'status',
        'sort',
    ];

    protected $casts = [
        'price' => 'integer',
        'price_sale' => 'integer',
        'sale' => 'integer',
        'stock' => 'integer',
        'status' => 'integer',
        'sort' => 'integer',
    ];

    /**
     * Quan hệ: variant thuộc về sản phẩm
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Helper: lấy giá hiển thị cho variant
     */
    public function getDisplayPriceAttribute()
    {
        return $this->price_sale > 0 ? $this->price_sale : $this->price;
    }

    /**
     * Helper: hiển thị đóng gói (ví dụ: "1 hộp x 20 viên")
     */
    public function getPackagingAttribute()
    {
        return "1 {$this->unit}" . ($this->quantity_per_unit ? " x {$this->quantity_per_unit}" : "");
    }
}
