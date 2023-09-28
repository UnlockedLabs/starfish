<?php

use App\Models\ProviderPlatform;

class ProviderUtil

{
    protected string $id;
    protected string $type;
    protected int    $account_id;
    protected string $account_name;
    protected string $access_key;
    protected string $base_url;
    protected string $icon_url;

    public function __construct(string $Type, int $accountId, string $AccountName, string $access_key, string $base_url, string $IconUrl)

    {
        // we will not have generated a provider ID yet, we we construct and call registerProvider()
        $this->type = $Type;
        $this->account_id = $accountId;
        $this->account_name = $AccountName;
        $this->access_key = $access_key;
        $this->base_url = $base_url;
        $this->icon_url = $IconUrl;
        $this->registerProvider();
    }

    // constructor for when we already have the providerId
    public static function getByProviderId($providerId): ProviderUtil | \InvalidArgumentException
    {
        $provider = ProviderPlatform::findByProviderId($providerId);

        if (!$provider) {
            throw new \InvalidArgumentException('Invalid provider ID');
        }
        return new self($provider->type, $provider->account_id, $provider->account_name, $provider->access_key, $provider->base_url, $provider->icon_url);
    }

    function registerProvider()
    {
        // Check if the provider already exists in the database, these fields are unique
        $existingProvider = ProviderPlatform::where([
            'base_url' => $this->base_url,
            'account_id' => $this->account_id,
            'type' => $this->type,
        ])->first();

        if (!$existingProvider) {

            // Create a new provider instance and save it to the database
            $newProvider = new ProviderPlatform([
                'type' => $this->type,
                'accountId' => $this->account_id,
                'account_name' => $this->account_name,
                'access_key' => $this->access_key,
                'base_url' => $this->base_url,
            ]);

            $newProvider->save();

            // if newly registered, we store and add created provider ID field
            $this->id = $newProvider->id;
        }

        // Provider already exists, return it
        return $existingProvider;
    }

    public function getProviderId(): string
    {
        return $this->id;
    }

    public function getProviderType(): string
    {
        return $this->type;
    }

    public function setProviderType(string $providerType): void
    {
        $this->type = $providerType;
    }

    public function getAccountId(): int
    {
        return $this->account_id;
    }

    public function setAccountId(int $accountId): void
    {
        $this->account_id = $accountId;
    }

    public function getAccountName(): string
    {
        return $this->account_name;
    }

    public function setInstanceId(string $accountName): void
    {
        $this->account_name = $accountName;
    }

    public function getbase_url(): string
    {
        return $this->base_url;
    }

    public function setbase_url(string $url): void
    {
        $this->base_url = $url;
    }

    public function getaccess_key(): string
    {
        return $this->access_key;
    }

    public function setaccess_key(string $key): void
    {
        $this->access_key = $key;
    }

    public function getIconUrl(): string
    {
        return $this->icon_url;
    }

    public function setIconUrl(string $url): void
    {
        $this->icon_url = $url;
    }
}
