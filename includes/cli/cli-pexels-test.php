<?php
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('pexels search', function ($args, $assoc_args) {
        $pexelsApiKey = $_ENV['pexels_api_key'] ?? null;

        if (!$pexelsApiKey) {
            WP_CLI::error('Pexels API key is missing. Set PEXELS_API_KEY in your .env file.');
        }

        try {
            $Pexels_API = new Pexels_API($pexelsApiKey);

            // Ensure a search query is provided
            if (empty($args[0])) {
                WP_CLI::error('Please provide a search query, e.g., "construction worker".');
            }

            $query = implode(' ', $args); // Combine all CLI arguments into a single string
            $perPage = $assoc_args['per_page'] ?? 10; // Number of results per page (default 10)
            $page = $assoc_args['page'] ?? 1; // Specific page (default 1)
            $limit = $assoc_args['limit'] ?? null; // Limit the number of images returned

            WP_CLI::log("Searching for photos with query: '{$query}'...");
            $photos = $Pexels_API->fetchPhotos($query, (int)$perPage, (int)$page);

            if (empty($photos)) {
                WP_CLI::warning('No photos found for the given query.');
                return;
            }

            // Apply the limit parameter if set
            if ($limit !== null) {
                $photos = array_slice($photos, 0, (int)$limit);
            }

            foreach ($photos as $photo) {
                // Extract file extension from URL
                $url = $photo['src']['original'];
                $extension = pathinfo($url, PATHINFO_EXTENSION);
                WP_CLI::log("{$url}");
            }

            WP_CLI::success('Photos successfully fetched from Pexels API.');
        } catch (Exception $e) {
            WP_CLI::error("Pexels API Error: " . $e->getMessage());
        }
    });

    WP_CLI::add_command('pexels import', function ($args, $assoc_args) {
        $pexelsApiKey = $_ENV['pexels_api_key'] ?? null;

        if (!$pexelsApiKey) {
            WP_CLI::error('Pexels API key is missing. Set PEXELS_API_KEY in your .env file.');
        }

        try {
            $Pexels_API = new Pexels_API($pexelsApiKey);

            // Ensure a search query is provided
            if (empty($args[0])) {
                WP_CLI::error('Please provide a search query, e.g., "construction worker".');
            }

            $query = implode(' ', $args);
            
            WP_CLI::log("Searching for a photo with query: '{$query}'...");
            $photos = $Pexels_API->fetchPhotos($query, 1, 1);

            if (empty($photos)) {
                WP_CLI::warning('No photos found for the given query.');
                return null;
            }

            $photo = $photos[0];
            $url = $photo['src']['original'];
            
            // Download and import the image to the media library
            WP_CLI::log("Importing photo to media library...");
            
            $upload = wp_upload_bits(basename($url), null, file_get_contents($url));
            
            if ($upload['error']) {
                WP_CLI::error("Failed to upload image: " . $upload['error']);
                return null;
            }

            // Prepare image metadata
            $attachment = array(
                'post_mime_type' => wp_check_filetype(basename($url))['type'],
                'post_title'     => sanitize_file_name($query),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            // Insert the image
            $attach_id = wp_insert_attachment($attachment, $upload['file']);
            
            if (is_wp_error($attach_id)) {
                WP_CLI::error("Failed to create attachment: " . $attach_id->get_error_message());
                return null;
            }

            // Generate metadata and thumbnails
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
            wp_update_attachment_metadata($attach_id, $attach_data);

            $image_url = wp_get_attachment_url($attach_id);
            WP_CLI::success("Photo successfully imported: {$image_url}");
            
            return $image_url;
        } catch (Exception $e) {
            WP_CLI::error("Pexels API Error: " . $e->getMessage());
            return null;
        }
    });
}
