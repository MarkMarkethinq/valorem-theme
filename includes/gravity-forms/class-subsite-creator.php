<?php

require_once dirname(__DIR__) . '/api/class-pexels-api.php';
require_once __DIR__ . '/class-subsite-content.php';
require_once __DIR__ . '/class-subsite-initalizer.php';

class SubsiteCreator
{
    private static $formId;
    private $pexelsApi;

    public function __construct()
    {
        self::$formId = $_ENV['intake_form_id'];
        $this->pexelsApi = new Pexels_API($_ENV['pexels_api_key']);
        add_action('gform_after_submission', [$this, 'handleFormSubmission'], 10, 2);
    }

    public function handleFormSubmission($entry, $form): void
    {
        if ((int)$form['id'] !== (int)self::$formId) {
            return;
        }

        $siteData = $this->extractFormData($form, $entry);
        $siteId = $this->createSubsite($siteData);

        if ($siteId) {
            // $contentGenerator = new SubsiteContent($siteData);
            // $content = $contentGenerator->generateAllContent();
            // $this->updateACFFields($siteId, $content);
            // $this->addAllImages($siteId);
        }
    }

    private function extractFormData(array $form, array $entry): array
    {
        $fields = [
            'domeinnaam' => 'Domeinnaam',
            'bedrijfsnaam' => 'Bedrijfsnaam',
            'E-mailadres' => 'E-mailadres',
            'Naam' => 'Naam',
        ];

        $data = [];
        foreach ($fields as $key => $label) {
            $fieldId = $this->findFieldIdByLabel($form, $label);
            $data[$key] = rgar($entry, $fieldId);
        }

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

    // private function addHeroImage(int $siteId): void
    // {
    //     try {
    //         $photos = $this->pexelsApi->fetchPhotos('painter artist professional', 1);
            
    //         if (empty($photos)) {
    //             \Includes\Logger::warning('Geen foto\'s gevonden via Pexels API');
    //             return;
    //         }

    //         $imageUrl = $photos[0]['src']['large'];
    //         $filename = 'hero-image-' . time() . '.jpg';
            
    //         switch_to_blog($siteId);
            
    //         require_once(ABSPATH . 'wp-admin/includes/file.php');
    //         require_once(ABSPATH . 'wp-admin/includes/image.php');
    //         require_once(ABSPATH . 'wp-admin/includes/media.php');
            
    //         $tmpFile = download_url($imageUrl);
            
    //         if (is_wp_error($tmpFile)) {
    //             \Includes\Logger::error('Fout bij downloaden afbeelding', ['error' => $tmpFile->get_error_message()]);
    //             restore_current_blog();
    //             return;
    //         }

    //         $file = [
    //             'name' => $filename,
    //             'type' => 'image/jpeg',
    //             'tmp_name' => $tmpFile,
    //             'error' => 0,
    //             'size' => filesize($tmpFile)
    //         ];

    //         $attachmentId = media_handle_sideload($file, 0);

    //         if (is_wp_error($attachmentId)) {
    //             \Includes\Logger::error('Fout bij uploaden afbeelding', ['error' => $attachmentId->get_error_message()]);
    //             @unlink($tmpFile);
    //             restore_current_blog();
    //             return;
    //         }

    //         $frontPageId = get_option('page_on_front');
            
    //         if (!$frontPageId) {
    //             $frontPageId = wp_insert_post([
    //                 'post_type' => 'page',
    //                 'post_title' => 'Home',
    //                 'post_status' => 'publish',
    //             ]);
                
    //             update_option('page_on_front', $frontPageId);
    //             update_option('show_on_front', 'page');
    //         }

    //         update_field('hero_image', $attachmentId, $frontPageId);
    //         restore_current_blog();
            
    //     } catch (Exception $e) {
    //         \Includes\Logger::error('Fout bij toevoegen hero afbeelding', ['error' => $e->getMessage()]);
    //         restore_current_blog();
    //     }
    // }

    public function createSubsite(array $siteData): ?int
    {
        \Includes\Logger::info('Starting subsite creation process', [
            'site_data' => $siteData
        ]);

        $networkId = get_current_network_id();
        $userId = get_current_user_id();

        \Includes\Logger::debug('Initial setup', [
            'network_id' => $networkId,
            'user_id' => $userId
        ]);

        $domain = strtolower(sanitize_text_field($siteData['domeinnaam']));
        $siteName = $siteData['bedrijfsnaam'] ?: $domain;
        $adminEmail = 'info@developing.nl';

        \Includes\Logger::debug('Domain and site details', [
            'domain' => $domain,
            'site_name' => $siteName,
            'admin_email' => $adminEmail
        ]);

        // Gebruik de volledige domeinnaam als host
        $host = $domain;
        $path = '/';

        \Includes\Logger::debug('Site creation details', [
            'host' => $host,
            'path' => $path
        ]);
        
        $newSite = wpmu_create_blog(
            $host,        // bijv. timmer123.nl
            $path,        // /
            $siteName,    // bedrijfsnaam
            $userId,
            ['public' => 1],
            $networkId
        );
    
        if (is_wp_error($newSite)) {
            \Includes\Logger::error('Failed to create subsite', [
                'error_message' => $newSite->get_error_message(),
                'error_code' => $newSite->get_error_code(),
                'error_data' => $newSite->get_error_data()
            ]);
            return null;
        }

        \Includes\Logger::info('Successfully created new blog', [
            'site_id' => $newSite
        ]);

        $initializer = new SubsiteInitializer((int)$newSite);
        
        \Includes\Logger::info('Starting site initialization', [
            'site_id' => $newSite
        ]);
        
        $initializer->initialize($siteData);
        
        \Includes\Logger::info('Completed subsite creation process', [
            'site_id' => $newSite,
            'site_url' => $host . $path
        ]);
    
        return (int)$newSite;
    }    

    private function updateFeatures(int $siteId, array $features): void {
        switch_to_blog($siteId);
        
        try {
            $frontPageId = get_option('page_on_front');
            
            // Update het features repeater veld
            update_field('features', $features, $frontPageId);
            
            \Includes\Logger::info('Features updated successfully', [
                'site_id' => $siteId,
                'features_count' => count($features)
            ]);
        } catch (\Exception $e) {
            \Includes\Logger::error('Error updating features', [
                'error' => $e->getMessage(),
                'site_id' => $siteId
            ]);
        } finally {
            restore_current_blog();
        }
    }

    private function updateACFFields(int $siteId, array $content): void {
        switch_to_blog($siteId);
        
        try {
            $frontPageId = get_option('page_on_front');
            
            if (!$frontPageId) {
                \Includes\Logger::error('Front page not found', ['site_id' => $siteId]);
                return;
            }

            // Update all ACF fields with the generated content
            foreach ($content as $fieldName => $fieldValue) {
                update_field($fieldName, $fieldValue, $frontPageId);
            }
            
            \Includes\Logger::info('ACF fields updated successfully', [
                'site_id' => $siteId,
                'updated_fields' => array_keys($content)
            ]);
        } catch (\Exception $e) {
            \Includes\Logger::error('Error updating ACF fields', [
                'error' => $e->getMessage(),
                'site_id' => $siteId
            ]);
        } finally {
            restore_current_blog();
        }
    }

    private function addAllImages(int $siteId): void {
        try {
            // Haal verschillende soorten afbeeldingen op voor verschillende secties
            $heroPhotos = $this->pexelsApi->fetchPhotos('painter artist professional', 1);
            $contentPhotos = $this->pexelsApi->fetchPhotos('painting house interior exterior', 2);
            
            if (empty($heroPhotos) || empty($contentPhotos)) {
                \Includes\Logger::warning('Geen voldoende foto\'s gevonden via Pexels API');
                return;
            }

            switch_to_blog($siteId);
            
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            
            $frontPageId = get_option('page_on_front');
            
            if (!$frontPageId) {
                $frontPageId = wp_insert_post([
                    'post_type' => 'page',
                    'post_title' => 'Home',
                    'post_status' => 'publish',
                ]);
                
                update_option('page_on_front', $frontPageId);
                update_option('show_on_front', 'page');
            }

            // Hero afbeelding
            $heroImage = $this->downloadAndUploadImage($heroPhotos[0]['src']['large'], 'hero-image');
            if ($heroImage) {
                update_field('hero_image', $heroImage, $frontPageId);
            }

            // Content afbeeldingen
            if (isset($contentPhotos[0])) {
                $leftImage = $this->downloadAndUploadImage($contentPhotos[0]['src']['large'], 'left-image');
                if ($leftImage) {
                    update_field('image_left', $leftImage, $frontPageId);
                }
            }

            if (isset($contentPhotos[1])) {
                $rightImage = $this->downloadAndUploadImage($contentPhotos[1]['src']['large'], 'right-image');
                if ($rightImage) {
                    update_field('image_right', $rightImage, $frontPageId);
                }
            }

            restore_current_blog();
            
        } catch (Exception $e) {
            \Includes\Logger::error('Fout bij toevoegen afbeeldingen', ['error' => $e->getMessage()]);
            restore_current_blog();
        }
    }

    private function downloadAndUploadImage(string $imageUrl, string $baseFilename): ?int {
        $filename = $baseFilename . '-' . time() . '.jpg';
        $tmpFile = download_url($imageUrl);
        
        if (is_wp_error($tmpFile)) {
            \Includes\Logger::error('Fout bij downloaden afbeelding', ['error' => $tmpFile->get_error_message()]);
            return null;
        }

        $file = [
            'name' => $filename,
            'type' => 'image/jpeg',
            'tmp_name' => $tmpFile,
            'error' => 0,
            'size' => filesize($tmpFile)
        ];

        $attachmentId = media_handle_sideload($file, 0);

        if (is_wp_error($attachmentId)) {
            \Includes\Logger::error('Fout bij uploaden afbeelding', ['error' => $attachmentId->get_error_message()]);
            @unlink($tmpFile);
            return null;
        }

        return $attachmentId;
    }
}

new SubsiteCreator();
