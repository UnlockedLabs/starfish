<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StudentMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        "student_id",
        "provider_user_id",
        "provider_platform_id",
        "consumer_user_id",
        "consumer_platform_id",
    ];

    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'student_id');
    }

    public function studentEnrollment(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class, 'provider_user_id');
    }

    public function providerPlatform(): HasOne
    {
        return $this->hasOne(ProviderPlatform::class, 'provider_platform_id');
    }

    public function consumerPlatform(): HasOne
    {
        return $this->hasOne(ConsumerPlatform::class,  'consumer_platform_id');
    }
}
