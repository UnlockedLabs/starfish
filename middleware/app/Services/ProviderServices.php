<?php

use App\Models\ProviderPlatform;
use App\Models\PlatformConnection;

class ProviderServices

{
    // Returns an instance of CanvasServices to make dynamic API calls.
    //*****************************************************
    //* @param int $providerId
    //* @return  CanvasServices
    //* @throws \InvalidArgumentException
    //*****************************************************
    public static function getCanvasServices(int $providerId): CanvasServices | \InvalidArgumentException
    {
        $provider = ProviderPlatform::findByProviderId($providerId);

        if (!$provider) {
            throw new \InvalidArgumentException('Invalid provider ID');
        }
        return new \CanvasServices($provider->provider_id, $provider->account_id, $provider->access_key, $provider->base_url);
    }

    // Turns a course into a LTI deep linking JSON structure
    /***************************************************************
     * @param string $courseId
     * @param string $courseName
     * @param string $url
     * @return string
    /* Example response:
    {
        "@context": "http://purl.imsglobal.org/ctx/lti/v1/ContentItem",
        "items": [
            {
                "type": "link",
                "title": "Course Name",
                "url": "https://canvas.instructure.com/api/v1/courses/123456"
            }
        ]
    }
     */
    public static function encodeDeepLinkingJson(string $courseId, string $courseName, string $url): string
    {
        // Create the LTI deep linking JSON structure
        $courseUrl = $url . "api/v1/courses/" . $courseId;

        $link = [
            'type' => 'link',
            'title' => $courseName,
            'url' => $courseUrl,
        ];
        $response = ['@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem', 'items' => $link];
        return json_encode($response);
    }

    // Registers a new Provider Platform + Platform connection
    /**********************************************
     *
     * @param int    $accountId
     * @param string $type
     * @param string $accountName
     * @param string $accessKey
     * @param string $baseUrl
     * @param string $iconUrl
     * @return       JsonResponse
     * @throws       \InvalidArgumentException
     *
     * Example response:
     * { "provider_id": 1, "platform_connection_id": 1, "provider_state": "Enabled" }
     ************************************************/
    // Registers a new Provider Platform, returns the provider ID
    public static function registerPlatformProvider(int $accountId, string $type, string $accountName, string $accessKey, string $baseUrl, string $iconUrl, int $consumerId): string
    {
        // Check if the provider already exists in the database, these fields are unique
        $existingProvider = ProviderPlatform::where([
            'base_url' => $baseUrl,
            'account_id' => $accountId,
            'type' => $type,
            'account_name' => $accountName,
        ])->first();

        if (!$existingProvider) {
            // Create a new provider instance and save it to the database
            $newProvider = new ProviderPlatform([
                'accountId' => $accountId,
                'account_name' => $accountName,
                'access_key' => $accessKey,
                'base_url' => $baseUrl,
                'icon_url' => $iconUrl,
            ]);
            $newProvider->save();
            $platformConn =  PlatformConnection::createAndRegister($consumerId, $newProvider->getProviderId());
            // if newly registered, we store and add created provider ID field
            $res = [
                'provider_id' => $newProvider->getProviderId(),
                'platform_connection_id' => $platformConn->id,
                'provider_state' => 'Enabled',
            ];
            return json_encode($res);
        } else {
            // if already registered, return it
            $connection = PlatformConnection::where([
                'consumer_platform_id' => $consumerId,
                'provider_platform_id' => $existingProvider->getProviderId(),
            ])->first();
            // Somehow this provider has not been registered.. This should never happen***************
            if (!$connection) {
                $connection =  PlatformConnection::createAndRegister($consumerId, $existingProvider->getProviderId());
            }
            $res = [
                'provider_id' => $connection->getProviderId(),
                'platform_connection_id' => $connection->id,
                'provider_state' => 'Enabled',
            ];
            return json_encode($res);
        }
    }
}
