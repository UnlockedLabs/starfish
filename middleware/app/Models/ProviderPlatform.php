<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProviderPlatform extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'description',
        'icon_url',
        'account_id',
        'access_key',
        'base_url',
    ];

    public function consumerPlatform(): BelongsToMany
    {
        return $this->belongsToMany(ConsumerPlatform::class, 'platform_connections')->withPivot('state', 'id');
    }
}
