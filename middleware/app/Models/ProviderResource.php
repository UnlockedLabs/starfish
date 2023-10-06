<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',          // Course ID
        'type',        // ENUM ProviderResourceType
        'provider_id', // ProviderPlatform ID
        'consumer_id', // ConsumerPlatform ID
    ];
    protected $casts = [
        'type' => ProviderResourceType::class,
    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
