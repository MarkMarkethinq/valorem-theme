<?php

use Includes\Logger;

/**
 * Class Magic_Login_Handler
 * 
 * Handles the creation and validation of magic login links for WordPress users
 * after Gravity Forms submission.
 */
class Magic_Login_Handler {
    /**
     * @var int The ID of the form that should trigger the magic login link
     */
    private $login_form_id;

    /**
     * @var object The email service instance
     */
    private $email_service;

    /**
     * Initialize the magic login handler
     */
    public function __construct() {
        // Get the form ID from environment variable if needed for form handling
        $this->login_form_id = $_ENV['login_form_id'] ?? null;

        // Include and initialize email service
        require_once get_template_directory() . '/includes/class-email-service.php';
        $this->email_service = new \Includes\Email_Service();

        // Only add form submission handler if form ID is set
        if ($this->login_form_id) {
            add_action('gform_after_submission', array($this, 'process_form_submission'), 10, 2);
        }

        add_action('init', array($this, 'handle_magic_login'));
    }

    /**
     * Process the form submission and send magic login link if applicable
     * 
     * @param array $entry The form entry
     * @param array $form The form object
     */
    public function process_form_submission($entry, $form) {
        // Only process submissions from the configured form
        if ($form['id'] != $this->login_form_id) {
            return;
        }

        // Get the email field from the form
        $email = $this->get_email_from_entry($entry, $form);
        
        if (!$email) {
            Logger::error('No email field found in form submission', [
                'form_id' => $form['id'],
                'entry_id' => $entry['id']
            ], 'magic_login');
            return;
        }

        // Check if user exists
        $user = get_user_by('email', $email);
        if (!$user) {
            Logger::warning('No user found with email', [
                'email' => $email,
                'form_id' => $form['id'],
                'entry_id' => $entry['id']
            ], 'magic_login');
            return;
        }

        // Generate and store token
        $token = $this->generate_login_token($user->ID);
        
        // Send email with magic link using the email service
        $this->send_magic_login_email($user, $token);

        Logger::info('Magic login link sent successfully', [
            'user_id' => $user->ID,
            'email' => $email
        ], 'magic_login');
    }

    /**
     * Get email from form entry
     * 
     * @param array $entry
     * @param array $form
     * @return string|false
     */
    private function get_email_from_entry($entry, $form) {
        foreach ($form['fields'] as $field) {
            if ($field->type === 'email') {
                return rgar($entry, $field->id);
            }
        }
        return false;
    }

    /**
     * Generate a unique login token and store it in user meta
     * 
     * @param int $user_id
     * @return string
     */
    public function generate_login_token($user_id) {
        $token = wp_generate_password(32, false);
        $expiry = time() + (48 * HOUR_IN_SECONDS);
        
        update_user_meta($user_id, '_magic_login_token', [
            'token' => $token,
            'expiry' => $expiry,
            'used' => false
        ]);
        
        return $token;
    }

    /**
     * Send the magic login email using the email service
     * 
     * @param WP_User $user
     * @param string $token
     */
    private function send_magic_login_email($user, $token) {
        $login_url = add_query_arg([
            'magic_login' => $token,
            'user' => $user->ID
        ], home_url());

        // Prepare email data
        $email_data = [
            'name' => $user->display_name,
            'company_name' => get_bloginfo('name'),
            'logo_url' => get_template_directory_uri() . '/templates/geregeld-online-homepage/img/logo.png',
            'message' => 'Je hebt een inlog-link aangevraagd. Gebruik onderstaande link om direct in te loggen op je account.',
            'cta_url' => $login_url,
            'cta_text' => 'Log direct in',
            'footer_text' => sprintf(
                'Deze link is 48 uur geldig en kan slechts één keer worden gebruikt. Als je deze link niet hebt aangevraagd, kun je deze email negeren.'
            )
        ];

        $this->email_service->send_email(
            $user->user_email,
            'Je inlog-link voor ' . get_bloginfo('name'),
            $email_data
        );
    }

    /**
     * Handle the magic login request
     */
    public function handle_magic_login() {
        if (!isset($_GET['magic_login']) || !isset($_GET['user'])) {
            return;
        }

        $token = sanitize_text_field($_GET['magic_login']);
        $user_id = absint($_GET['user']);
        
        $stored_data = get_user_meta($user_id, '_magic_login_token', true);
        
        if (!$stored_data || 
            $stored_data['token'] !== $token || 
            $stored_data['used'] || 
            $stored_data['expiry'] < time()) {
            wp_die('Deze inlog-link is ongeldig of verlopen.', 'Ongeldige link');
            return;
        }

        // Mark token as used
        $stored_data['used'] = true;
        update_user_meta($user_id, '_magic_login_token', $stored_data);

        // Log the user in
        wp_set_auth_cookie($user_id, true);
        
        // Redirect to homepage or dashboard
        wp_redirect(home_url('/'));
        exit;
    }
}

// Initialize the handler
new Magic_Login_Handler(); 