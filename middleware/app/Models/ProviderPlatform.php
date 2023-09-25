<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProviderPlatform extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'description',
        'icon_url',
        'access_key',
        'base_url',
    ];

    protected $hidden = [
        'access_key',
    ];

    protected $casts = [
        'type' => ProviderPlatformType::class
    ];

    public function platformConnections(): HasMany
    {
        return $this->hasMany(PlatformConnections::class);
    }
}
