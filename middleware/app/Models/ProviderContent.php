<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProviderContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_content_id',           // Course ID
        'type',                  // Enum ProvdiderContentType
        'external_resource_id',  // External Resource ID
        'provider_platform_id',   // ProviderPlatform ID
    ];

    protected $casts = [
        'type' => ProviderContentType::class,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function studentEnrollment(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class, 'provider_content_id');
    }

    public function providerPlatform(): BelongsTo
    {
        return $this->belongsTo(ProviderPlatform::class, 'provider_platform_id');
    }
}
