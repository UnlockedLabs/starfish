<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'content_id' => $this->content_id,
            'type' => $this->type,
            'external_resource_id' => $this->external_resource_id,
            'provider_id' => $this->provider_id,
        ];
    }
}
