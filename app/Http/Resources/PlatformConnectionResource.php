<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Http\Request;

class PlatformConnectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'consumer_platform_id' => $this->consumer_platform_id,
            'provider_platform_id' => $this->provider_platform_id,
            'state' => $this->state,
        ];
    }
}
