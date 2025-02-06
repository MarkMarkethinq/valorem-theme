<?php

require_once dirname(__FILE__) . '/../api/class-chatgpt-api.php';
require_once dirname(__FILE__) . '/../api/class-transip-api.php';
require_once dirname(__FILE__) . '/../utils/class-logger.php';

class DomainSuggestionHandler {
    private $chatgpt_api;
    private $transip_api;
 
    public function __construct() {
        $this->chatgpt_api = new ChatGPT_API($_ENV['chatgpt_api_key']);
        
        $this->transip_api = new TransIP_API(
            $_ENV['transip_api_login_name'],    
            $_ENV['transip_api_private_key'],   
            true,                               
            true                                
        );
        
        add_action('wp_ajax_get_domain_suggestion', array($this, 'get_domain_suggestion'));
        add_action('wp_ajax_nopriv_get_domain_suggestion', array($this, 'get_domain_suggestion'));
        
        // Verplaats deze actie naar functions.php
        remove_action('wp_enqueue_scripts', array($this, 'localize_script'));
    }

    public function localize_script() {
        \includes\Logger::info('Localize script aangeroepen met intake_form_id: ' . $_ENV['intake_form_id']);
        wp_localize_script('domain-suggestions', 'domainSuggestions', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'intakeFormId' => $_ENV['intake_form_id']
        ));
    }

    public function get_domain_suggestion() {
        \includes\Logger::info('Domain suggestion request ontvangen');
        
        try {
            // Check of alle benodigde data aanwezig is
            if (!isset($_POST['company_name'])) {
                \includes\Logger::error('Bedrijfsnaam ontbreekt in POST data');
                wp_send_json_error([
                    'message' => 'Bedrijfsnaam ontbreekt',
                    'data' => null
                ]);
                return;
            }

            // Controleer of we op de juiste pagina zijn
            if (!isset($_POST['page']) || $_POST['page'] !== '2') {
                \includes\Logger::error('Ongeldige pagina voor domain suggestion');
                wp_send_json_error([
                    'message' => 'Ongeldige pagina voor domain suggestion',
                    'data' => null
                ]);
                return;
            }

            $company_name = sanitize_text_field($_POST['company_name']);
            \includes\Logger::info('Verwerken domain suggestion voor bedrijf: ' . $company_name);
            
            $suggestion = $this->generate_domain_suggestion($company_name);
            
            wp_send_json_success([
                'domain' => $suggestion,
                'message' => 'Domein suggestie succesvol gegenereerd'
            ]);
            
        } catch (Exception $e) {
            \includes\Logger::error('Fout bij genereren domeinnaamsuggestie: ' . $e->getMessage());
            wp_send_json_error([
                'message' => 'Er is een fout opgetreden bij het genereren van de domeinnaam: ' . $e->getMessage(),
                'data' => null
            ]);
        }
    }

    private function getDomainPrompt(string $company_name): string {
        return "Genereer een UNIEKE domeinnaam voor '{$company_name}' dat DIRECT herkenbaar is aan de bedrijfsnaam.\n\n" .
        "Strikte regels:\n" .
        "1. Gebruik ALLEEN exacte woorden uit de bedrijfsnaam (géén vertalingen/afkortingen)\n" .
        "2. Behoud de originele woordvolgorde waar mogelijk\n" .
        "3. Verwijder alleen generieke toevoegingen zoals 'BV', 'Groep' of 'Energie' bij lengteproblemen\n" .
        "4. Maximaal 2 koppeltekens tussen originele woorden\n" .
        "5. Behoud kernidentiteit: blijf zo dicht mogelijk bij de originele naam\n\n" .
        "Technische eisen:\n" .
        "1. Exact 1 .nl-extensie\n" .
        "2. Allemaal kleine letters (geen cijfers/speciale tekens)\n" .
        "3. Gebruik koppeltekens tussen woorden als de naam lang is of onduidelijk wordt zonder koppeltekens\n" .
        "4. Maximaal 20 karakters (inclusief .nl)\n\n" .
        "Geef alleen de domeinnaam, geen extra tekst.";
    }

    private function generate_domain_suggestion(string $company_name): string {
        try {
            $maxAttempts = 5;
            $attempt = 0;
            $usedDomains = [];
            
            while ($attempt < $maxAttempts) {
                $attempt++;
                \includes\Logger::info("Poging $attempt van $maxAttempts voor domein generatie");
                
                // Voeg een poging-specifieke instructie toe aan de prompt
                $attemptPrompt = $this->getDomainPrompt($company_name);
                if ($attempt > 1) {
                    $attemptPrompt .= "\nMaak het ANDERS dan deze eerder geprobeerde domeinen: " . implode(", ", $usedDomains);
                }
                
                $response = $this->chatgpt_api->generateChat([
                    [
                        'role' => 'user',
                        'content' => $attemptPrompt
                    ]
                ]);
                
                $domain = strtolower(trim($response));
                $usedDomains[] = $domain;
                
                if (!$this->isValidDomain($domain)) {
                    \includes\Logger::warning("Ongeldig domein formaat: $domain");
                    continue;
                }
                
                try {
                $availability = $this->transip_api->checkDomain($domain);
                \includes\Logger::info("Domein beschikbaarheid check voor $domain: $availability");
                
                if ($availability === 'free') {
                    \includes\Logger::info("Beschikbaar domein gevonden: $domain");
                    return $domain;
                }
                
                \includes\Logger::warning("Domein $domain is niet beschikbaar (status: $availability)");
                } catch (Exception $e) {
                    \includes\Logger::error("Fout bij controleren domein $domain: " . $e->getMessage());
                    continue;
                }
            }
            
            throw new \Exception("Geen beschikbaar domein gevonden na $maxAttempts pogingen. Geprobeerde domeinen: " . implode(", ", $usedDomains));
            
        } catch (Exception $e) {
            \includes\Logger::error('Fout in generate_domain_suggestion: ' . $e->getMessage());
            throw $e;
        }
    }

    private function isValidDomain(string $domain): bool {
        return preg_match('/^[a-z0-9][a-z0-9-]*[a-z0-9]\.nl$/', $domain) === 1;
    }
}

new DomainSuggestionHandler();
