<?php


class ProviderUtil

{
    public $providerId; // integer: Id for the particular instance of the LMS (might be moved to the .config/.env file)
    public $providerName; // Name of the LMS/Provider
    public $ltiAccount; // Their unique Lti Provider account

    private $db; // Database connection
    public function __construct($providerId, $name, $ltiAccount)

    {
        $this->providerId = $providerId;
        $this->providerName = $name;
        $this->ltiAccount = $ltiAccount;
    }

    // LTIProvider.php (Model)


}
