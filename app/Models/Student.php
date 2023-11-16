<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasUuids;
    use HasFactory;

    public function studentMapping(): HasOne
    {
        return $this->hasOne(StudentMapping::class, 'student_id');
    }

    public function studentEnrollments(): HasManyThrough
    {
        return $this->hasManyThrough(StudentEnrollment::class, StudentMapping::class, 'student_id', 'provider_user_id', 'id');
    }
}
