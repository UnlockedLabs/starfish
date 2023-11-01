<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderUserResource extends Model
{
    use HasFactory;
    protected $fillable = [
        "user_id",              // ProviderUser ID
        "provider_resource_id", // Course ID
        "provider_id",          // Platform Provider ID
        "status",            // Enum (ProviderUserResourceStatus)
    ];

    protected $casts = [
        'status' => ProviderUserResourceStatus::class,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        // we need to link the provdider id to the PlatformProvider Model
        // and the provider_resource_id to the ProviderResource Model
        $this->belongsTo(PlatformProvider::class, 'provider_id', 'id');
        $this->belongsTo(ProviderResource::class, 'provider_resource_id', 'id');
        $this->belongsTo(ProviderUser::class, 'user_id', 'id');
    }
}
