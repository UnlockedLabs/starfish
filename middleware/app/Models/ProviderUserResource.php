<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderUserResource extends Model
{
    use HasFactory;
    protected $fillable = [
        "user_id",
        "provider_resource_id",
        "provider_id",
        "completed",
    ];
}
