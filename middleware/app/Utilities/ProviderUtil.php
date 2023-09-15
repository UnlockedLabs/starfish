<?php

use App\Models\LMSProvider;

class ProviderUtil

{
    /// My Provider table has the following fields:
    /// providerId VARCHAR(255) PRIMARY KEY, (our UUID we give to the specific LMS + instance + account)
    /// providerType CARCHAR(255) NOT NULL, (Canvas-cloud, canvas-oss, blackboard, moodle, etc)
    /// instanceId   VARCHAR(255) NOT NULL, (the id for the specific instance of the LMS)
    /// accoundId    VARCHAR(255) NOT NULL, (the id for the specific account of the provider on the LMS)
    /// url          VARCHAR(255) NOT NULL, (the base url to the instance of the LMS)
    /// apiKey
    protected string $providerId;
    protected string $providerType;
    protected int    $accountId;
    protected string $instanceId;
    protected string $url;


    public function __construct(string $providerType, int $accountId, string $instanceId, string $url)

    {
        // we will not have generated a provider UUID yet, we we construct and call registerProvider()
        $this->providerType = $providerType;
        $this->accountId = $accountId;
        $this->url = $url;
        $this->instanceId = $this->$instanceId;
        // Register the provider in the database and generate providerID
        $this->registerProvider();
    }

    public function registerProvider()
    {
        // Check if the provider already exists in the database, these fields are unique
        $existingProvider = LMSProvider::where([
            'instanceId' => $this->instanceId,
            'accountId' => $this->accountId,
            'providerType' => $this->providerType,
        ])->first();

        if (!$existingProvider) {
            // Generate UUID based on instanceId, accountId, and providerType
            $uuid = $this->generateUUID();

            // Create a new provider instance and save it to the database
            $newProvider = new LMSProvider([
                'providerId' => $uuid,
                'providerType' => $this->providerType,
                'instanceId' => $this->instanceId,
                'accountId' => $this->accountId,
                'url' => $this->url,
            ]);

            $newProvider->save();

            // if newly registered, we store and add created provider ID field
            $this->providerId = $uuid;
        }

        // Provider already exists, return it
        return $existingProvider;
    }

    private function generateUUID()
    {
        // Just an example of how we could generate a UUID for the specific provider
        return md5($this->instanceId . $this->accountId . $this->providerType);
    }

    public function getProviderId(): string
    {
        return $this->providerId;
    }

    public function getProviderType(): string
    {
        return $this->providerType;
    }

    public function setProviderType(string $providerType): void
    {
        $this->providerType = $providerType;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function setAccountId(int $accountId): void
    {
        $this->accountId = $accountId;
    }

    public function getInstanceId(): string
    {
        return $this->instanceId;
    }

    public function setInstanceId(string $instanceId): void
    {
        $this->instanceId = $instanceId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
