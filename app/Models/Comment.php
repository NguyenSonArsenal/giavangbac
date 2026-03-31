<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends BaseModel
{
    use SoftDeletes;

    protected $table = 'comments';

    protected $fillable = [
        'post_id',
        'parent_id',
        'name',
        'email',
        'body',
        'is_admin',
        'status',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'status'   => 'integer',
        'post_id'  => 'integer',
        'parent_id' => 'integer',
    ];

    const STATUS_APPROVED = 1;
    const STATUS_PENDING  = -1;

    // ── Relations ──

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    // ── Scopes ──

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRootComments($query)
    {
        return $query->whereNull('parent_id');
    }

    // ── Helpers ──

    public function getInitialAttribute()
    {
        return strtoupper(mb_substr($this->name, 0, 1));
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
