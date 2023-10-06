<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PlatformConnectionType;

class ProviderPlatform extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'type',
        'name',
        'account_id',
        'access_key',
        'base_url',
        'icon_url',
    ];

    protected $casts = [
        'type' => PlatformConnectionType::class,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
