<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        "provider_user_id",              // Student ID in our system (maps to appropriate ID)
        "provider_content_id",           // Course ID
        "provider_platform_id",          // Provider Platform ID
        "status",                        // Enum (ProviderUserResourceStatus)
    ];

    protected $casts = [
        'status' => StudentEnrollmentStatus::class,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function studentMapping(): BelongsTo
    {
        return $this->belongsTo(StudentMapping::class, 'provider_user_id');
    }

    public function providerContent(): BelongsTo
    {
        return $this->belongsTo(ProviderContent::class, 'provider_content_id');
    }

    public function providerPlatform(): BelongsTo
    {
        return $this->belongsTo(ProviderPlatform::class, 'provider_platform_id');
    }
}
