<?php

namespace Database\Factories;

use App\Models\ProviderPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderPlatformFactory extends Factory
{
    protected $model = ProviderPlatform::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'type' => 'canvas_cloud',
            'description' => $this->faker->sentence,
            'icon_url' => $this->faker->imageUrl,
            'account_id' => $this->faker->randomNumber,
            'access_key' => $this->faker->unique()->word,
            'base_url' => $this->faker->url,
        ];
    }
}
