<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends BaseModel
{
    use SoftDeletes;

    protected $table = 'admin';

    protected $fillable = [
        'email',
        'password',
    ];
}
