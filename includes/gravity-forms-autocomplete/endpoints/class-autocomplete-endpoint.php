<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles the server-side request to Overheid.io (OpenKVK) and returns suggestions.
 */
class OpenKVK_Autocomplete_Endpoint {

    public static function handle_autocomplete() {
        // Security check: Make sure the nonce is valid.
        check_ajax_referer( 'gf-openkvk-autocomplete-nonce', 'nonce' );

        // Get the query parameter
        $query = isset( $_GET['q'] ) ? sanitize_text_field( $_GET['q'] ) : '';
        if ( empty( $query ) ) {
            wp_send_json_error( array( 'message' => 'No query provided.' ), 400 );
        }

        // Create a cache key based on the query
        $cache_key = 'openkvk_autocomplete_' . md5( $query );
        $last_refresh_key = 'openkvk_last_refresh';

        // Check if the cache needs to be refreshed
        $last_refresh = get_transient( $last_refresh_key );
        if ( $last_refresh === false || date( 'Y-m-d' ) !== date( 'Y-m-d', $last_refresh ) ) {
            // Clear all cached queries and update the refresh timestamp
            self::clear_all_cached_queries();
            set_transient( $last_refresh_key, time(), DAY_IN_SECONDS );
        }

        // Check if cached results exist
        $cached_results = get_transient( $cache_key );
        if ( $cached_results !== false ) {
            wp_send_json_success( $cached_results );
        }

        // Build the request URL for Overheid.io
        $url = 'https://api.overheid.io/suggest/openkvk/' . urlencode( $query ) . '?size=5';

        // Make the HTTP GET request
        $response = wp_remote_get(
            $url,
            array(
                'headers' => array(
                    'ovio-api-key' => $_ENV['overheid_io_api_development_key'],
                ),
                'timeout' => 10,
            )
        );

        // Check for errors in the response
        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => $response->get_error_message() ), 500 );
        }

        if ( ! $response || ! isset( $response['body'] ) ) {
            wp_send_json_error( array( 'message' => 'Invalid response from API.' ), 500 );
        }

        // Decode the JSON body of the response
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body );

        // Controleer of "handelsnaam" bestaat en een array is
        if (isset($data->handelsnaam) && is_array($data->handelsnaam)) {
          $results = array();

          // Verwerk de bedrijfsnamen en plaatsen
          foreach ($data->handelsnaam as $item) {
              if (isset($item->handelsnaam) && isset($item->plaats)) {
                  $results[] = array(
                      'handelsnaam' => $item->handelsnaam,
                      'plaats' => $item->plaats,
                  );
              }
          }

          // Cache de resultaten en stuur naar de frontend
          set_transient($cache_key, $results, DAY_IN_SECONDS);
          wp_send_json_success($results);
        } else {
          // Stuur een lege array als er geen resultaten zijn
          set_transient($cache_key, array(), DAY_IN_SECONDS);
          wp_send_json_success(array());
        }

    }

    /**
     * Clears all cached queries (transients) related to OpenKVK autocomplete.
     */
    private static function clear_all_cached_queries() {
        global $wpdb;

        // Delete all transients starting with "openkvk_autocomplete_"
        $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_openkvk_autocomplete_%' OR option_name LIKE '_transient_timeout_openkvk_autocomplete_%'"
        );
    }
}
