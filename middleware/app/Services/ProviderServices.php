<?php

use App\Models\ProviderPlatform;

class ProviderServices

{
    // Returns an instance of CanvasServices to make API calls.
    //*****************************************************
    //* @param int $providerId
    //* @return CanvasServices
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

    /**********************************************
     * Registers a new Provider Platform
     *
     * @param int    $accountId
     * @param string $type
     * @param string $accountName
     * @param string $accessKey
     * @param string $baseUrl
     * @param string $iconUrl
     * @return        string
     ************************************************/
    // Registers a new Provider Platform, returns the provider ID
    public static function registerPlatformProvider(int $accountId, string $type, string $accountName, string $accessKey, string $baseUrl, string $iconUrl): string
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

            // if newly registered, we store and add created provider ID field
            return $newProvider->id;
        } else {
            // if already registered, we return the existing provider ID field
            return $existingProvider->id;
        }
    }
}
