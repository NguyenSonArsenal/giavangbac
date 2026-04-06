<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class GoldPriceHistory extends Model
{
    protected $table = 'metal_prices';

    protected $fillable = [
        'metal_type',
        'source',
        'unit',
        'buy_price',
        'sell_price',
        'price_date',
        'recorded_at',
    ];

    protected static function booted()
    {
        static::addGlobalScope('metal_type', function (Builder $builder) {
            $builder->where('metal_type', 'gold');
        });

        static::creating(function ($model) {
            if (empty($model->metal_type)) {
                $model->metal_type = 'gold';
            }
        });
    }

    protected $casts = [
        'buy_price'   => 'integer',
        'sell_price'  => 'integer',
        'price_date'  => 'date',
        'recorded_at' => 'datetime',
    ];

    /**
     * Lấy lịch sử N ngày theo nguồn và đơn vị, gom nhóm theo ngày (lấy bản ghi cuối mỗi ngày).
     * Lọc theo price_date (ngày giá trị) thay vì recorded_at để tránh bị ảnh hưởng
     * bởi ON UPDATE CURRENT_TIMESTAMP khi update bản ghi.
     */
    public static function getHistory(string $source, string $unit, int $days): \Illuminate\Support\Collection
    {
        $fromDate = now()->subDays($days)->startOfDay()->toDateString();

        return static::where('source', $source)
            ->where('unit', $unit)
            ->where('price_date', '>=', $fromDate)
            ->orderBy('price_date')
            ->orderBy('recorded_at')
            ->get()
            ->groupBy(function ($r) {
                return $r->price_date->format('Y-m-d');
            })
            ->map(function ($group) {
                return $group->last(); // 1 điểm / ngày (bản ghi cuối)
            });
    }

    /**
     * Lấy tất cả bản ghi trong ngày hôm nay (intraday) để vẽ chart 1D.
     * Không group by ngày, trả về từng điểm theo giờ:phút.
     */
    public static function getIntradayHistory(string $source, string $unit): \Illuminate\Support\Collection
    {
        return static::where('source', $source)
            ->where('unit', $unit)
            ->where('recorded_at', '>=', now()->startOfDay())
            ->orderBy('recorded_at')
            ->get();
    }
}
