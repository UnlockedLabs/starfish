<?php

use App\Models\ConsumerPlatform;

class ConsumerPlatformServices
{
    public static function createConsumerPlatform($type, $name, $api_key, $base_url)
    {
        $consumerPlatform = new ConsumerPlatform();
        $consumerPlatform->type = $type;
        $consumerPlatform->name = $name;
        $consumerPlatform->api_key = $api_key;
        $consumerPlatform->base_url = $base_url;
        $consumerPlatform->save();
        return $consumerPlatform;
    }
}
