<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumer_platform_id',
        'provider_platform_id',
        // 'state',
    ];
}