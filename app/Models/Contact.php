<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends BaseModel
{
    protected $table = 'contact';
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    // Constants for status
    const STATUS_UNREAD = -1;
    const STATUS_READ = 1;

    /**
     * Check if contact is unread
     */
    public function isUnread()
    {
        return $this->status === self::STATUS_UNREAD;
    }

    /**
     * Check if contact is read
     */
    public function isRead()
    {
        return $this->status === self::STATUS_READ;
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        $this->update(['status' => self::STATUS_READ]);
    }

    /**
     * Mark as unread
     */
    public function markAsUnread()
    {
        $this->update(['status' => self::STATUS_UNREAD]);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->status === self::STATUS_READ ? 'Đã đọc' : 'Chưa đọc';
    }
}
