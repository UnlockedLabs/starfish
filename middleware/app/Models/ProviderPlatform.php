<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'id' => 'integer',
        'type' => 'string',
        'account_id' => 'integer',
        'name' => 'string',
        'access_key' => 'string',
        'base_url' => 'string',
        'icon_url' => 'string',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

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
    }
}
