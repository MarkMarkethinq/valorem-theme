<?php


require_once __DIR__ . '/vendor/autoload.php';


try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} catch (Exception $e) {
    die('Error loading .env file: ' . $e->getMessage());
}

// Load all necessary includes
$includes = [
    'includes/utils/class-logger.php', // Zorg dat Logger als eerste wordt ingeladen
    'includes/templates.php',
    
    'includes/gravity-forms-autocomplete/class-gravity-forms-autocomplete.php',

    'includes/gravity-forms/class-subsite-contact-form.php',
    'includes/gravity-forms/class-intake-form.php',
    'includes/gravity-forms/class-subsite-creator.php',
    'includes/gravity-forms/class-subsite-initalizer.php',
    'includes/gravity-forms/class-zzp-form.php',
    //'includes/gravity-forms/class-partial-entry-continu.php',
    'includes/gravity-forms/class-magic-login-handler.php', // Magic Login functionality
    'includes/api/class-chatgpt-api.php',
    'includes/ajax/domain-suggestions.php',
    //'includes/api/class-pexels-api.php',
    'includes/api/class-transip-api.php',
    //'includes/gf-to-multisite.php',
    'includes/theme-setup.php',
    'includes/acf-options.php',
    'includes/class-admin-bar-customizer.php',
    //'includes/feedback-widget.php',
    'includes/enqueue-scripts.php',
    'includes/popups/gf-done-popup.php', // Popup toevoegen
    'includes/popups/first-time-popup.php', // Popup toevoegen
    'includes/gravity-forms/class-stripe-ideal.php',
];

foreach ($includes as $file) {
    if (file_exists(get_template_directory() . '/' . $file)) {
        require_once get_template_directory() . '/' . $file;
    }
}

// Load WP-CLI scripts if WP_CLI is defined
if (defined('WP_CLI')) {
    $cli_scripts = [
        'includes/cli/cli-create-subsite.php',
        'includes/cli/cli-delete-all-subsites.php',
        'includes/cli/cli-chatgpt-test.php',
        'includes/cli/cli-pexels-test.php',
        'includes/cli/cli-transip-test.php',
        // Voeg hier extra CLI-scripts toe indien nodig
    ];

    foreach ($cli_scripts as $file) {
        if (file_exists(get_template_directory() . '/' . $file)) {
            require_once get_template_directory() . '/' . $file;
        }
    }
}

add_action('after_setup_theme', function () {
    $defaults = array(
        'height' => 100,
        'width' => 400,
        'flex-height' => true,
        'flex-width' => true,
        'header-text' => array('site-title', 'site-description'),
        'unlink-homepage-logo' => true,
    );
    add_theme_support('custom-logo', $defaults);
});

/**
 * Initialize Feedback Tool
 */
//require_once get_template_directory() . '/includes/feedback-tool/class-feedback-tool.php';

// Register feedback post type
/*add_action('init', function() {
    register_post_type('feedback', array(
        'labels' => array(
            'name' => 'Feedback',
            'singular_name' => 'Feedback Item',
            'menu_name' => 'Feedback',
            'all_items' => 'Alle Feedback',
            'add_new' => 'Nieuwe Feedback',
            'add_new_item' => 'Nieuwe Feedback Toevoegen',
            'edit_item' => 'Feedback Bewerken',
            'view_item' => 'Bekijk Feedback',
            'search_items' => 'Zoek Feedback',
            'not_found' => 'Geen feedback gevonden',
            'not_found_in_trash' => 'Geen feedback gevonden in prullenbak'
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-feedback',
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'custom-fields'
        ),
        'menu_position' => 25,
        'has_archive' => false
    ));
});

// Initialize feedback tool
$feedback_tool = new Feedback_Tool();
$feedback_tool->init();


 * Initialize Support Tool
 
require_once get_template_directory() . '/includes/feedback-support-tool/class-feedback-support-tool.php';

// Register support ticket post type
add_action('init', function() {
    register_post_type('support_ticket', array(
        'labels' => array(
            'name' => 'Support Tickets',
            'singular_name' => 'Support Ticket',
            'menu_name' => 'Support',
            'all_items' => 'Alle Tickets',
            'add_new' => 'Nieuw Ticket',
            'add_new_item' => 'Nieuw Ticket Toevoegen',
            'edit_item' => 'Ticket Bewerken',
            'view_item' => 'Bekijk Ticket',
            'search_items' => 'Zoek Tickets',
            'not_found' => 'Geen tickets gevonden',
            'not_found_in_trash' => 'Geen tickets gevonden in prullenbak'
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-tickets-alt',
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array(
            'title',
            'editor',
            'custom-fields'
        ),
        'menu_position' => 26,
        'has_archive' => false
    ));
});

// Initialize support tool
$support_tool = new Feedback_Support_Tool();
$support_tool->init();
*/

// Flowbite en popup scripts laden
function enqueue_popup_files() {
    // Flowbite CSS en JS
    wp_enqueue_style('flowbite', 'https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css');
    wp_enqueue_script('flowbite', 'https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_popup_files');

/**
 * Verwijder de zoekbalk uit de admin bar
 */
function remove_admin_bar_search() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('search');
}
add_action('wp_before_admin_bar_render', 'remove_admin_bar_search');

/*add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
    show_admin_bar(false);
}*/

/**
 * Customize admin bar color based on environment
 */
function customize_admin_bar_color() {
    if (!is_admin_bar_showing()) {
        return;
    }

    $current_site_url = get_site_url();
    $style = '';

    if (strpos($current_site_url, 'geregeld.online') !== false) {
        $style = '
            #wpadminbar {
                background: #d63638 !important;
            }
            #wpadminbar .ab-item, 
            #wpadminbar a.ab-item, 
            #wpadminbar > #wp-toolbar span.ab-label {
                color: #fff !important;
            }
            #wpadminbar .ab-icon, 
            #wpadminbar .ab-icon:before,
            #wpadminbar .ab-item:before,
            #wpadminbar .ab-item:after {
                color: #fff !important;
            }
        ';
    } elseif (strpos($current_site_url, 'multi2.local') !== false) {
        $style = '
            #wpadminbar {
                background: #666666 !important;
            }
            #wpadminbar .ab-item, 
            #wpadminbar a.ab-item, 
            #wpadminbar > #wp-toolbar span.ab-label {
                color: #fff !important;
            }
            #wpadminbar .ab-icon, 
            #wpadminbar .ab-icon:before,
            #wpadminbar .ab-item:before,
            #wpadminbar .ab-item:after {
                color: #fff !important;
            }
        ';
    }

    if ($style) {
        echo '<style type="text/css">' . $style . '</style>';
    }
}
add_action('admin_head', 'customize_admin_bar_color');
add_action('wp_head', 'customize_admin_bar_color');

// Add initialization of the new class
$admin_bar_customizer = new Admin_Bar_Customizer();
$admin_bar_customizer->init();

// Initialize Magic Login Admin
require_once get_template_directory() . '/includes/gravity-forms/class-magic-login-admin.php';

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('domain-suggestions', get_template_directory_uri() . '/assets/js/domain-suggestions.js', array('jquery'), null, true);
    wp_localize_script('domain-suggestions', 'domainSuggestions', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'intakeFormId' => $_ENV['intake_form_id']
    ));
});

// AJAX handlers voor domain suggestions
function handle_domain_suggestion() {
    if (!isset($_POST['company_name'])) {
        wp_send_json_error(['message' => 'Bedrijfsnaam ontbreekt']);
        return;
    }

    try {
        $domain_suggester = new DomainSuggestionHandler();
        $domain_suggester->get_domain_suggestion(); // Gebruik de publieke methode
    } catch (Exception $e) {
        wp_send_json_error([
            'message' => 'Er is een fout opgetreden',
            'error' => $e->getMessage()
        ]);
    }
}

// Registreer de AJAX handlers voor zowel ingelogde als niet-ingelogde gebruikers
add_action('wp_ajax_get_domain_suggestion', 'handle_domain_suggestion');
add_action('wp_ajax_nopriv_get_domain_suggestion', 'handle_domain_suggestion');

// Voorkom dat er e-mails worden verzonden bij het aanmaken van een nieuwe subsite
add_action('wpmu_new_blog', function ($blog_id, $user_id, $domain, $path, $site_id, $meta) {
    add_filter('wp_mail', function ($args) {
        // Zorg ervoor dat alle e-mails leeg worden gemaakt
        $args['to'] = '';
        $args['subject'] = '';
        $args['message'] = '';
        return $args;
    });
}, 10, 6);


