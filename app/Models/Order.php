<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';

    protected $fillable = [
        'code',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'note',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'integer',
        'status'       => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Label trạng thái đơn hàng
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            0 => 'Chờ xác nhận',
            1 => 'Đã xác nhận',
            2 => 'Đang giao hàng',
            3 => 'Hoàn thành',
            4 => 'Đã huỷ',
            default => 'Không rõ',
        };
    }
}
