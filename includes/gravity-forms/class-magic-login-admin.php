<?php

/**
 * Class Magic_Login_Admin
 * 
 * Handles the admin interface for generating magic login links
 */
class Magic_Login_Admin {
    /**
     * @var Magic_Login_Handler The magic login handler instance
     */
    private $login_handler;

    /**
     * Initialize the admin interface
     */
    public function __construct() {
        // Only initialize in multisite network admin
        if (!is_multisite()) {
            return;
        }

        require_once get_template_directory() . '/includes/gravity-forms/class-magic-login-handler.php';
        $this->login_handler = new Magic_Login_Handler();

        add_action('network_admin_menu', array($this, 'add_network_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_generate_magic_link', array($this, 'ajax_generate_magic_link'));
    }

    /**
     * Add the network admin menu item
     */
    public function add_network_admin_menu() {
        add_menu_page(
            'Magic Login Links',
            'Magic Login',
            'manage_network',
            'magic-login',
            array($this, 'render_admin_page'),
            'dashicons-admin-links',
            30
        );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'toplevel_page_magic-login') {
            return;
        }

        wp_enqueue_script(
            'magic-login-admin',
            get_template_directory_uri() . '/includes/gravity-forms/assets/js/magic-login-admin.js',
            array('jquery'),
            '1.0.0',
            true
        );

        wp_localize_script('magic-login-admin', 'magicLoginAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('generate_magic_link')
        ));
    }

    /**
     * Render the admin page
     */
    public function render_admin_page() {
        // Check if user has network admin capabilities
        if (!current_user_can('manage_network')) {
            wp_die(__('Sorry, you are not allowed to access this page.'));
            return;
        }

        ?>
        <div class="wrap">
            <h1>Magic Login Links</h1>
            <p>Genereer een eenmalige inlog-link voor een gebruiker.</p>

            <div class="card">
                <h2>Genereer Link</h2>
                <table class="form-table">
                    <tr>
                        <th><label for="user_email">E-mailadres</label></th>
                        <td>
                            <input type="email" id="user_email" class="regular-text" />
                            <p class="description">Voer het e-mailadres van de gebruiker in</p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button id="generate_link" class="button button-primary">Genereer Link</button>
                </p>

                <div id="magic_link_result" style="display: none;">
                    <h3>Gegenereerde Link</h3>
                    <div class="magic-link-container">
                        <input type="text" id="magic_link" class="large-text" readonly />
                        <button id="copy_link" class="button">Kopieer Link</button>
                    </div>
                    <p class="description">Deze link is 48 uur geldig en kan slechts één keer worden gebruikt.</p>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Handle AJAX request to generate magic link
     */
    public function ajax_generate_magic_link() {
        check_ajax_referer('generate_magic_link');

        if (!current_user_can('manage_network')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        $email = sanitize_email($_POST['email'] ?? '');
        if (!$email) {
            wp_send_json_error('Invalid email address');
            return;
        }

        $user = get_user_by('email', $email);
        if (!$user) {
            wp_send_json_error('User not found');
            return;
        }

        $token = $this->login_handler->generate_login_token($user->ID);
        $login_url = add_query_arg([
            'magic_login' => $token,
            'user' => $user->ID
        ], home_url());

        wp_send_json_success(array(
            'login_url' => $login_url
        ));
    }
}

// Initialize the admin interface
new Magic_Login_Admin(); 