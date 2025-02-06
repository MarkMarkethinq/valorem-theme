<?php

class SubsiteCreator
{
    private const FORM_ID = 1;
    private const JSON_UPLOAD_DIR = '/gravity-forms-json/';

    public function __construct()
    {
        add_action('gform_after_submission', [$this, 'handleFormSubmission'], 10, 2);
    }

    public function handleFormSubmission($entry, $form): void
    {
        if ((int)$form['id'] !== self::FORM_ID) {
            return;
        }

        $siteData = $this->extractFormData($form, $entry);

        if (empty($siteData['site_name']) || empty($siteData['admin_email'])) {
            return;
        }

        $this->saveFormEntryAsJson($form, $entry);

        $newSiteId = $this->createSubsite($siteData);

        if ($newSiteId) {
            $this->createUserForSubsite(
                $newSiteId,
                $siteData['username'] ?? 'admin_' . sanitize_title($siteData['site_name']),
                $siteData['admin_email'],
                $siteData['bedrijfsnaam'] ?? '',
                $siteData['first_name'] ?? '',
                $siteData['last_name'] ?? ''
            );

            $this->switchTheme($newSiteId);
            $this->fillSiteContent($newSiteId, $siteData);
            $this->setLogoForSubsite($newSiteId, $siteData['logo'] ?? null);
        }
    }

    private function fillSiteContent(int $siteId, array $siteData): void
    {
        switch_to_blog($siteId);
        update_field('field_hero_title', 'Welkom op de website van: ' . $siteData['admin_email'], 'option');
        restore_current_blog();
    }

    private function setLogoForSubsite(int $siteId, ?string $logoUrl): void
    {
        if (!$logoUrl) {
            return;
        }

        switch_to_blog($siteId);

        $imageId = media_sideload_image($logoUrl, 0, null, 'id');
        if (!is_wp_error($imageId)) {
            update_field('hero_image', $imageId, 'option');
        }

        restore_current_blog();
    }

    private function switchTheme(int $siteId): void
    {
        switch_to_blog($siteId);
        switch_theme('go-theme');
        restore_current_blog();
    }

    private function extractFormData(array $form, array $entry): array
    {
        $fields = [
            'site_name' => 'Wat is je gewenste domeinnaam? (inclusief alternatieven)',
            'admin_email' => 'Email',
            'fullname' => 'Naam',
            'bedrijfsnaam' => 'Bedrijfsnaam',
            'email_display_choice' => 'Mogen wij je email tonen op de website?',
            'logo' => 'Upload hier je logo',
        ];

        $data = [];
        foreach ($fields as $key => $label) {
            $fieldId = $this->findFieldIdByLabel($form, $label);
            $data[$key] = rgar($entry, $fieldId);
        }

        // Split fullname into first and last name
        $fullnameFieldId = $this->findFieldIdByLabel($form, $fields['fullname']);
        $data['first_name'] = rgar($entry, $fullnameFieldId . '.3');
        $data['last_name'] = rgar($entry, $fullnameFieldId . '.6');

        return $data;
    }

    private function findFieldIdByLabel(array $form, string $label): ?int
    {
        foreach ($form['fields'] as $field) {
            if ($field->label === $label) {
                return $field->id;
            }
        }
        return null;
    }

    private function createSubsite(array $siteData): ?int
    {
        $networkId = get_current_network_id();
        $userId = get_current_user_id();
    
        $subdomain = sanitize_title($siteData['site_name']);
        $siteUrl = $subdomain . '.' . $_SERVER['SERVER_NAME'];
    
        // Create the subsite
        $newSite = wpmu_create_blog(
            $siteUrl,
            '/',
            $siteData['site_name'],
            $userId,
            ['public' => 1],
            $networkId
        );
    
        // Check for errors
        if (is_wp_error($newSite)) {
            error_log('Failed to create subsite: ' . $newSite->get_error_message());
            return null;
        }
    
        return (int)$newSite; // Return the site ID
    }
    

    private function createUserForSubsite(
        int $blogId,
        string $username,
        string $email,
        string $bedrijfsnaam,
        string $firstName,
        string $lastName
    ): void {
        switch_to_blog($blogId);

        $userData = [
            'user_login' => $username,
            'user_email' => $email,
            'user_pass' => wp_generate_password(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'role' => 'administrator',
        ];

        $newUserId = wp_insert_user($userData);

        if (!is_wp_error($newUserId)) {
            add_user_to_blog($blogId, $newUserId, 'administrator');
            wp_send_new_user_notifications($newUserId, 'both');
        }

        restore_current_blog();
    }

    private function saveFormEntryAsJson(array $form, array $entry): void
    {
        $uploadDir = wp_upload_dir();
        $uploadPath = $uploadDir['basedir'] . self::JSON_UPLOAD_DIR;

        if (!file_exists($uploadPath)) {
            wp_mkdir_p($uploadPath);
        }

        $data = $this->extractEntryData($form, $entry);
        $fileName = sprintf('form-%d-%d.json', $form['id'], time());
        $filePath = $uploadPath . $fileName;

        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function extractEntryData(array $form, array $entry): array
    {
        $data = [];
        foreach ($form['fields'] as $field) {
            if (isset($field->inputs) && is_array($field->inputs)) {
                foreach ($field->inputs as $input) {
                    $value = rgar($entry, (string)$input['id']);
                    if ($value !== null) {
                        $data[$field->label][$input['label']] = $value;
                    }
                }
            } elseif ($field->type === 'checkbox') {
                $data[$field->label] = array_filter(
                    array_map(
                        fn($choice) => rgar($entry, $field->id . '.' . $choice['value']) ? $choice['text'] : null,
                        $field->choices
                    )
                );
            } else {
                $value = rgar($entry, $field->id);
                if ($value !== null) {
                    $data[$field->label] = $value;
                }
            }
        }
        return $data;
    }
}

// Initialize the SubsiteCreator class
new SubsiteCreator();
