<?php

/**
 * Class SubsiteInitializer
 *
 * Handles the initialization of a newly created subsite.
 */
class SubsiteInitializer
{
    private $siteId;

    public function __construct(int $siteId)
    {
        $this->siteId = $siteId;
    }

    public function initialize(array $siteData): void
    {
        switch_to_blog($this->siteId);

        try {
            $this->setTheme('go-theme'); 
            $this->createUser(
                $siteData['E-mailadres'],
                $this->generateUsername($siteData['Naam'], $siteData['bedrijfsnaam']),
                'administrator',
                $siteData['Naam']
            );
            $this->sendEmail($siteData['domeinnaam'], $siteData['E-mailadres']);
            $this->fillContent($siteData['bedrijfsnaam']);
            $this->copyACFFields();
        } catch (\Exception $e) {
            \Includes\Logger::error('Error during subsite initialization', ['error' => $e->getMessage()]);
        } finally {
            restore_current_blog();
        }

        \Includes\Logger::info('Subsite initialization complete', ['siteId' => $this->siteId]);
    }

    private function setTheme(string $themeSlug): void
    {
        if (wp_get_theme($themeSlug)->exists()) {
            switch_theme($themeSlug);
        } else {
            \Includes\Logger::warning("Theme '{$themeSlug}' does not exist.");
        }
    }

    private function createUser(string $email, string $username, string $role, string $displayName): void
    {
        if (email_exists($email)) {
            \Includes\Logger::warning('Email already exists', ['email' => $email]);
            return;
        }

        $password = wp_generate_password();
        $userId = wp_create_user($username, $password, $email);

        if (!is_wp_error($userId)) {
            wp_update_user([
                'ID' => $userId,
                'role' => $role,
                'display_name' => $displayName
            ]);
            
            wp_new_user_notification($userId, null, 'user');
        } else {
            \Includes\Logger::error('Failed to create user', [
                'error' => $userId->get_error_message(),
                'username' => $username,
                'email' => $email
            ]);
        }
    }

    private function generateUsername(string $name, string $company): string
    {
        $baseUsername = sanitize_user(
            strtolower(
                str_replace(' ', '', $name) . '_' . str_replace(' ', '', $company)
            )
        );
        
        $username = $baseUsername;
        $counter = 1;
        
        while (username_exists($username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }

    private function sendEmail(string $domain, string $recipient): void
    {
        $subject = "Your new site {$domain} is ready!";
        $message = "Congratulations! Your new site at {$domain} has been successfully created.";
        wp_mail($recipient, $subject, $message);
    }

    private function fillContent(string $siteName): void
    {
        // Maak een nieuwe pagina met de Schilder template
        $page_id = wp_insert_post([
            'post_title'     => $siteName,
            'post_content'   => '',  // Content komt uit ACF velden
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'page_template'  => 'templates/schilder.php'  // Pad naar template vanaf theme root
        ]);

        if (is_wp_error($page_id)) {
            \Includes\Logger::error('Failed to create page', [
                'error' => $page_id->get_error_message(),
                'site_name' => $siteName
            ]);
            return;
        }

        // Stel deze pagina in als homepage
        update_option('page_on_front', $page_id);
        update_option('show_on_front', 'page');

        // Stel de bedrijfsnaam in als ACF veld waarde
        if (function_exists('update_field')) {
            update_field('bedrijfnaam', $siteName, $page_id);
            \Includes\Logger::info('Company name set in ACF field', [
                'field' => 'bedrijfnaam',
                'value' => $siteName,
                'page_id' => $page_id
            ]);
        }

        \Includes\Logger::info('Homepage created successfully', [
            'page_id' => $page_id,
            'template' => 'schilder.php',
            'site_name' => $siteName
        ]);
    }

    private function copyACFFields(): void
    {
        if (!function_exists('acf_import_field_group')) {
            \Includes\Logger::error('ACF is not active');
            return;
        }

        $json_file = get_template_directory() . '/json/schilder-template.json';
        
        if (!file_exists($json_file)) {
            \Includes\Logger::error('ACF JSON file not found', ['file' => $json_file]);
            return;
        }
        
        // Lees het volledige JSON bestand
        $json_content = file_get_contents($json_file);
        $field_groups = json_decode($json_content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            \Includes\Logger::error('JSON decode error', [
                'error' => json_last_error_msg(),
                'raw_content' => substr($json_content, 0, 1000)
            ]);
            return;
        }

        // Controleer of we een array van field groups hebben
        if (!is_array($field_groups)) {
            \Includes\Logger::error('Invalid JSON structure - expected array of field groups');
            return;
        }

        switch_to_blog($this->siteId);
        
        try {
            foreach ($field_groups as $field_group) {
                // Importeer elke field group
                $new_group = acf_import_field_group($field_group);
                
                if (!$new_group) {
                    throw new \Exception('Failed to import ACF field group: ' . $field_group['title']);
                }
                
                \Includes\Logger::info('Field group imported successfully', [
                    'title' => $field_group['title'],
                    'key' => $field_group['key']
                ]);
            }
        } catch (\Exception $e) {
            \Includes\Logger::error('Error importing ACF fields', [
                'error' => $e->getMessage(),
                'site_id' => $this->siteId
            ]);
        } finally {
            restore_current_blog();
        }
    }
}
