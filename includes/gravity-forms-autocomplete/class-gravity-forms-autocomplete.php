<?php
/**
 * Require Gravity Forms custom classes only if GF is active.
 */
add_action( 'init', function() {

    // Ensure Gravity Forms is loaded
    if ( class_exists( 'GFCommon' ) ) {

        // 3. Hook everything up in an OOP style
        if ( ! class_exists( 'My_Theme_GravityForms_Integration' ) ) {

            class My_Theme_GravityForms_Integration {

                public function __construct() {
                    require_once get_stylesheet_directory() . '/includes/gravity-forms-autocomplete/fields/class-autocomplete-field.php';
                    require_once get_stylesheet_directory() . '/includes/gravity-forms-autocomplete/endpoints/class-autocomplete-endpoint.php';

                    
                    // Register the custom field
                    add_action( 'init', array( $this, 'register_openkvk_field' ));

                    // Enqueue script for autocomplete
                    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_openkvk_script' ) );

                    // Setup AJAX endpoints
                    add_action( 'wp_ajax_my_openkvk_autocomplete', array( 'OpenKVK_Autocomplete_Endpoint', 'handle_autocomplete' ) );
                    add_action( 'wp_ajax_nopriv_my_openkvk_autocomplete', array( 'OpenKVK_Autocomplete_Endpoint', 'handle_autocomplete' ) );
                }

                /**
                 * Enqueue the JS for our autocomplete on the front end.
                 */
                public function enqueue_openkvk_script() {

                    // (Optional) Only enqueue if a GF form is present on page
                    // For a quick approach, just enqueue globally:
                    wp_enqueue_script(
                        'gf-openkvk-autocomplete',
                        get_stylesheet_directory_uri() . '/includes/gravity-forms-autocomplete/scripts/autocomplete.js',
                        array( 'jquery' ),
                        time(),
                        true
                    );

                    // Localize script to send AJAX URL & nonce
                    wp_localize_script(
                        'gf-openkvk-autocomplete',
                        'gfOpenKvkAutocomplete',
                        array(
                            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                            'nonce'   => wp_create_nonce( 'gf-openkvk-autocomplete-nonce' ),
                        )
                    );

                    wp_enqueue_style(
                        'gf-openkvk-autocomplete-styles',
                        get_stylesheet_directory_uri() . '/includes/gravity-forms-autocomplete/scripts/autocomplete.css',
                        array(),
                        time()
                    );
                    
                }
            }

            // Initialize our integration
            new My_Theme_GravityForms_Integration();
        }
    }
});
