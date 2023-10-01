<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumerPlatform extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'api_key',
        'base_url',
    ];
}
