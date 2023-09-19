<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class LMSProvider extends Model
{
    protected $table = 'providers';

    protected $fillable = [
        'providerId',
        'providerType',
        'instanceId',
        'accountId',
        'url',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function findByProviderId(string $providerId): LMSProvider
    {
        return self::where('providerId', $providerId)->first();
    }

    public static function listByProviderType(string $providerType): Collection
    {
        return self::where('providerType', $providerType)->get();
    }
}
