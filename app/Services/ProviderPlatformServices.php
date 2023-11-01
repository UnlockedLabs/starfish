<?php

use App\Models\ProviderPlatform;
use App\Models\PlatformConnection;

class ProviderPlatformServices
{
    // Returns an instance of CanvasServices to make dynamic API calls.
    //*****************************************************
    //* @param int $providerId
    //* @return  CanvasServices
    //* @throws \InvalidArgumentException
    //*****************************************************
    public function getCanvasServices(int $providerId): CanvasServices | \InvalidArgumentException
    {
        $provider = ProviderPlatform::where('id', $providerId)->first();
        if (!$provider) {
            throw new \InvalidArgumentException('Invalid provider ID');
        }
        return new \CanvasServices($provider->provider_id, $provider->account_id, $provider->access_key, $provider->base_url);
    }

    // Creates a new Provider Platform
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
     ************************************************/
    // Registers a new Provider Platform, returns the provider ID
    public static function createProviderPlatform(int $accountId, string $type, string $accountName, string $accessKey, string $baseUrl, string $iconUrl): \Illuminate\Http\JsonResponse
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
            return response()->json(json_encode($newProvider, JSON_PRETTY_PRINT));
        } else {
            return response()->json(json_encode($existingProvider, JSON_PRETTY_PRINT));
        }
    }

    public static function formatLtiDeepLinkFromCanvasCourse(string $canvasCourseJSON, string $baseUrl)
    {
        $canvasCourseData = json_decode($canvasCourseJSON, true);
        // Mimick the LTI deep linking structure
        $ltiDeepLink = [
            "type" => "ltiResourceLink",
            "title" => $canvasCourseData["name"],
            "text" => $canvasCourseData["public_description"],
            "url" => $baseUrl . "/courses/" . $canvasCourseData["id"],
            "custom" => [
                "courseId" => $canvasCourseData["id"],
            ],
        ];
        // Convert the array to JSON
        $ltiDeepLinkJSON = json_encode($ltiDeepLink, JSON_PRETTY_PRINT);

        return $ltiDeepLinkJSON;
    }


    /***************************************************************
     * Create a new PlatformConnection and register the connection *
     ***************************************************************
     * @param int $consumerPlatformId
     * @param int $providerPlatformId
     * @return PlatformConnection
     */
    public static function createPlatformConnection(int $consumerPlatformId, int $providerPlatformId): PlatformConnection
    {
        $connection = new PlatformConnection();
        $connection->consumer_platform_id = $consumerPlatformId;
        $connection->provider_platform_id = $providerPlatformId;
        $connection->state = 'disabled';
        $connection->save();

        return $connection;
    }
}
