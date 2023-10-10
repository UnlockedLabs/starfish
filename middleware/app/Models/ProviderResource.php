<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_id',                   // Course ID
        'type',                 // ENUM ProviderResourceType
        'external_resource_id', // External Resource ID
        'provider_id',          // ProviderPlatform ID
    ];
    protected $casts = [
        'type' => ProviderResourceType::class,
    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
