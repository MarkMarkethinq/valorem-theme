<?php

if (defined('WP_CLI') && WP_CLI) {
    /**
     * Command to create a subsite via WP-CLI.
     */
    WP_CLI::add_command('subsite create', function ($args, $assoc_args) {
        $domain = $assoc_args['domain'] ?? null;
        $adminEmail = $assoc_args['admin_email'] ?? 'info@developing.nl';

        // Validate required arguments
        if (!$domain) {
            WP_CLI::error('The --domain parameter is required.');
        }

        // Prepare site data
        $siteData = [
            'domeinnaam' => $domain,
            'adminEmail' => $adminEmail,
        ];

        try {
            // Include the SubsiteCreator class if not already included
            if (!class_exists('SubsiteCreator')) {
                require_once get_template_directory().'';
            }

            $subsiteCreator = new SubsiteCreator();

            // Call createSubsite manually
            $siteId = $subsiteCreator->createSubsite($siteData);

            if ($siteId) {
                WP_CLI::success("Subsite created successfully with ID: {$siteId}");
            } else {
                WP_CLI::error('Failed to create subsite.');
            }
        } catch (Exception $e) {
            WP_CLI::error("An error occurred: " . $e->getMessage());
        }
    });
}
