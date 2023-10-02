<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/*
 *     d: Integer

    UserId: Integer

    ProviderResourceId: Integer

    ProviderId: Integer

    Completed: boolean (we can check the date on the course object)

*/

class UserResource extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "provider_resource_id",
        "provider_id",
        "completed",
    ];

    protected $asserts = [
        "user_id" => "integer",
        "provider_resource_id" => "integer",
        "provider_id" => "integer",
        "completed" => "boolean"
    ];
}
