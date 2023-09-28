<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD:app/Models/ConsumerPlatform.php
use Illuminate\Database\Eloquent\Relations\HasMany;
=======
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Models/ConsumerPlatform.php

class ConsumerPlatform extends Model
{
    use HasFactory;
<<<<<<< HEAD:app/Models/ConsumerPlatform.php

    protected $fillable = [
        'type',
        'name',
        'api_key',
        'base_url',
    ];

    protected $hidden = [
        'api_key',
    ];

    public function platformConnections(): HasMany
    {
        return $this->hasMany(PlatformConnections::class);
    }
=======
>>>>>>> 8b2e792 (fix: upgraded to meet schema):middleware/app/Models/ConsumerPlatform.php
}
