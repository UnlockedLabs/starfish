<?php

namespace App\Models;

<<<<<<< HEAD:app/Models/ProviderPlatform.php


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
=======
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Models/ProviderPlatform.php

class ProviderPlatform extends Model
{
    use HasFactory;

<<<<<<< HEAD:app/Models/ProviderPlatform.php
    protected $fillable = [
        'type',
        'name',
        'description',
=======
    protected $table = 'providers';

    protected $fillable = [
        'id',
        'type',
        'name',
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Models/ProviderPlatform.php
        'account_id',
        'access_key',
        'base_url',
        'icon_url',
    ];

<<<<<<< HEAD:app/Models/ProviderPlatform.php
=======
    protected $casts = [
        'id' => 'integer',
        'type' => 'string',
        'account_id' => 'integer',
        'name' => 'string',
        'access_key' => 'string',
        'base_url' => 'string',
        'icon_url' => 'string',
    ];
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Models/ProviderPlatform.php

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

<<<<<<< HEAD:app/Models/ProviderPlatform.php

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
=======
    public static function getAllProviderIds(): array
    {
        $providerIds = [];
        $providers = self::all();
        foreach ($providers as $provider) {
            $providerIds[] = $provider->providerId;
        }
        return $providerIds;
    }

    public static function findByProviderId(string $providerId): ProviderPlatform
    {
        return self::where('providerId', $providerId)->first();
    }

    public static function listByProviderType(string $providerType): Collection
    {
        return self::where('providerType', $providerType)->get();
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Models/ProviderPlatform.php
    }
}
