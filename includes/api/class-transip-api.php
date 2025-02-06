<?php

use Transip\Api\Library\TransipAPI;
use Transip\Api\Library\Entity\Domain;
use Transip\Api\Library\Entity\Domain\DnsEntry;
use Transip\Api\Library\Entity\Domain\Nameserver;

class TransIP_API
{
    private TransipAPI $api;
    private bool $testMode;

    /**
     * Constructor for initializing the TransIP API.
     *
     * @param string $login The TransIP login name
     * @param string $privateKey The private key for the TransIP API
     * @param bool $generateWhitelistOnlyTokens If the token should only be usable by whitelisted IPs
     * @param bool $testMode Whether to use test mode
     */
    public function __construct(
        string $login = '',
        string $privateKey = '',
        bool $generateWhitelistOnlyTokens = true,
        bool $testMode = false
    ) {
        $this->testMode = $testMode;
        $this->initializeApi($login, $privateKey, $generateWhitelistOnlyTokens);
    }

    /**
     * Initialize the TransIP API client
     */
    private function initializeApi(string $login, string $privateKey, bool $generateWhitelistOnlyTokens): void
    {
        if ($this->testMode && empty($login) && empty($privateKey)) {
            $demoToken = $_ENV['transip_api_demo_token'] ?? '';
            if (empty($demoToken)) {
                throw new \Exception('Demo token not found in environment variables');
            }
            $this->api = new TransipAPI('demo', $demoToken);
        } else {
            $this->api = new TransipAPI($login, $privateKey, $generateWhitelistOnlyTokens);
        }

        if ($this->testMode) {
            $this->api->setTestMode(true);
        }
    }

    /**
     * Test the API connection.
     *
     * @return bool True if the connection is successful, false otherwise
     */
    public function testConnection(): bool
    {
        return $this->api->test()->test();
    }

    /**
     * Get a list of all domains.
     *
     * @return array List of domains
     */
    public function getAllDomains(): array
    {
        return $this->api->domains()->getAll();
    }

    /**
     * Check if a domain name is available and return a user-friendly response.
     *
     * @param string $domainName The domain name to check
     * @return string A message indicating the domain's availability ('free' or other status)
     */
    public function checkDomain(string $domainName): string
    {
        try {
            \includes\Logger::info("TransIP API: Controleren domein beschikbaarheid voor: $domainName");
            $availabilityResult = $this->api->domainAvailability()->checkDomainName($domainName);
            $status = $availabilityResult->getStatus();
            \includes\Logger::info("TransIP API: Status voor $domainName is: $status");
            return $status;
        } catch (Exception $e) {
            \includes\Logger::error("TransIP API: Fout bij controleren domein: " . $e->getMessage());
            throw new \Exception('Error checking domain: ' . $e->getMessage());
        }
    }

    /**
     * Register a new domain name.
     *
     * @param string $domainName The domain name to register (without extension)
     * @param string $extension The domain extension (e.g. 'nl', 'com')
     * @return array{success: bool, message: string} Registration result with status and message
     */
    public function registerDomain(string $domainName, string $extension = 'nl'): array
    {
        try {
            $fullDomain = $this->buildFullDomainName($domainName, $extension);
            
            if (!$this->isDomainAvailable($fullDomain)) {
                return $this->createErrorResponse("Domain $fullDomain is not available for registration");
            }

            $this->api->domains()->register($fullDomain);
            return $this->createSuccessResponse("Domain $fullDomain has been successfully registered");
            
        } catch (Exception $e) {
            return $this->createErrorResponse('Error registering domain: ' . $e->getMessage());
        }
    }

    /**
     * Build a full domain name from name and extension
     */
    private function buildFullDomainName(string $name, string $extension): string 
    {
        return $name . '.' . $extension;
    }

    /**
     * Check if a domain is available for registration
     */
    private function isDomainAvailable(string $domain): bool
    {
        return $this->checkDomain($domain) === 'free';
    }

    /**
     * Create a success response array
     */
    private function createSuccessResponse(string $message): array
    {
        return [
            'success' => true,
            'message' => $message
        ];
    }

    /**
     * Create an error response array
     */
    private function createErrorResponse(string $message): array
    {
        return [
            'success' => false,
            'message' => $message
        ];
    }
}

// Initialize API if environment variables are set
add_action('init', function() {
    if (isset($_ENV['transip_api_login_name'], $_ENV['transip_api_private_key'])) {
        $transipApi = new TransIP_API(
            $_ENV['transip_api_login_name'],
            $_ENV['transip_api_private_key']
        );
    }
});

// Wijzig de filter voor form validatie
add_filter('gform_validation_7', function($validation_result) {
    $form = $validation_result['form'];
    
    // Zoek het domein veld op basis van veldnaam
    $domain_field = null;
    foreach($form['fields'] as $field) {
        if($field->label === 'Domeinnaam') {
            $domain_field = $field;
            break;
        }
    }
    
    if(!$domain_field) {
        \Includes\Logger::warning("Domeinnaam veld niet gevonden in formulier");
        return $validation_result;
    }
    
    $domain = rgpost('input_' . $domain_field->id);
    
    if (empty($domain)) {
        \Includes\Logger::warning("Geen domein ingevuld in formulier");
        return $validation_result;
    }

    try {
        // Initialiseer de TransIP API
        $transipApi = new TransIP_API(
            $_ENV['transip_api_login_name'],
            $_ENV['transip_api_private_key']
        );

        // Test eerst de API-verbinding
        if (!$transipApi->testConnection()) {
            \Includes\Logger::error("Geen verbinding mogelijk met TransIP API");
            
            // Bij connectie problemen, voeg een admin notice toe maar laat de validatie doorgaan
            add_action('admin_notices', function() {
                echo '<div class="error"><p>TransIP API verbinding mislukt. Controleer de inloggegevens.</p></div>';
            });
            
            return $validation_result;
        }

        // Als de verbinding succesvol is, controleer domein beschikbaarheid
        $isDomainAvailable = $transipApi->checkDomain($domain);

        if ($isDomainAvailable === 'free') {
            \Includes\Logger::info("Domein {$domain} is beschikbaar", [
                'form_id' => $form['id']
            ]);
            
            return $validation_result;
            
        } else {
            \Includes\Logger::warning("Domein {$domain} is niet beschikbaar", [
                'form_id' => $form['id'],
                'status' => $isDomainAvailable
            ]);
            
            $validation_result['is_valid'] = false;
            $domain_field->failed_validation = true;
            $domain_field->validation_message = 'Dit domein is helaas niet beschikbaar. Kies een ander domein.';
        }

    } catch (Exception $e) {
        \Includes\Logger::error("Fout bij controleren domein beschikbaarheid", [
            'error' => $e->getMessage(),
            'domain' => $domain,
            'form_id' => $form['id']
        ]);
        
        // Bij technische fout toch door laten gaan
        return $validation_result;
    }

    return $validation_result;
    
});
