<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentEnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'provider_user_id' => $this->provider_user_id,
            'provider_content_id' => $this->provider_content_id,
            'provider_platform_id' => $this->provider_platform_id,
            'status' => $this->status,
        ];
    }
}
