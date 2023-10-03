<?php

namespace App\Models;

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
}
