<?php
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('subsites delete-all', function ($args, $assoc_args) {
        // Bevestig actie met de gebruiker
        if (!WP_CLI::confirm('Are you sure you want to delete all subsites?')) {
            WP_CLI::log('Action canceled.');
            return;
        }

        // Haal alle subsite-ID's op
        $site_ids = get_sites([
            'network_id' => get_current_network_id(),
            'fields'     => 'ids',
        ]);

        if (empty($site_ids)) {
            WP_CLI::success('No subsites found.');
            return;
        }

        // Itereer over alle sites
        foreach ($site_ids as $site_id) {
            if ($site_id == 1) { // Hoofdsite overslaan
                WP_CLI::log("Skipping main site with ID: {$site_id}");
                continue;
            }

            // Verwijder de subsite
            try {
                WP_CLI::log("Deleting subsite with ID: {$site_id}");
                wp_delete_site($site_id);
            } catch (Exception $e) {
                WP_CLI::warning("Failed to delete site with ID: {$site_id}. Error: " . $e->getMessage());
            }
        }

        WP_CLI::success('All subsites have been deleted.');
    });
}
