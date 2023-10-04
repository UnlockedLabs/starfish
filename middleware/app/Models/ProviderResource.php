<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_id', // Course ID
        'type', // ProviderResourceType (enum)
        'provider_id', // ProviderPlatform ID
        'consumer_id', // ConsumerPlatform ID
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
