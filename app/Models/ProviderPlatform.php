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
        'account_id',
        'access_key',
        'base_url',
        'icon_url',
    ];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }


    public function platformConnections(): HasMany
    {
        return $this->hasMany(PlatformConnection::class, "provider_platform_id");
    }

    public function providerContent(): HasMany
    {
        return $this->hasMany(ProviderContent::class, "provider_platform_id");
    }

    public function studentMapping(): HasMany
    {
        return $this->hasMany(StudentMapping::class, "provider_platform_id");
    }
}
