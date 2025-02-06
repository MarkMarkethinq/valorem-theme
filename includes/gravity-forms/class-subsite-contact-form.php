<?php
/**
 * Class SubSiteContactForm
 *
 * Handles rendering Gravity Forms from the main site and dynamic email routing.
 */
class SubSiteContactForm {

    /**
     * Gravity Form ID.
     * @var int
     */
    private $form_id;

    /**
     * Main site ID.
     * @var int
     */
    private $main_site_id;

    /**
     * Constructor.
     *
     * @param int $form_id Gravity Form ID.
     * @param int $main_site_id Main site ID.
     */
    public function __construct($form_id, $main_site_id) {
        $this->form_id = $form_id;
        $this->main_site_id = $main_site_id;

        // Register hooks
        add_shortcode('contact_form', [$this, 'renderGravityForm']);
        add_filter('gform_notification', [$this, 'handleDynamicEmailRouting'], 10, 3);
    }

    /**
     * Render Gravity Form from the main site.
     *
     * @return string Gravity Form HTML output.
     */
    public function renderGravityForm() {
        // Switch to the main site
        switch_to_blog($this->main_site_id);

        // Render the form
        $form_output = gravity_form($this->form_id, false, false, false, null, true, false, false);

        // Restore to the current subsite
        restore_current_blog();

        return $form_output;
    }

    /**
     * Handle dynamic email routing for the contact form.
     *
     * @param array $notification Gravity Forms notification settings.
     * @param array $form Gravity Forms form data.
     * @param array $entry Gravity Forms entry data.
     * @return array Modified notification settings.
     */
    public function handleDynamicEmailRouting($notification, $form, $entry) {
        // Get the current subsite ID
        $current_blog_id = get_current_blog_id();

        // Switch to the current subsite
        switch_to_blog($current_blog_id);

        // Retrieve the email address from ACF options (replace 'e-mailadres' with your field name)
        $subsite_email = get_field('E-mailadres', 'option');

        // Restore to the main site
        restore_current_blog();

        // Update the notification email address if available
        if (!empty($subsite_email)) {
            $notification['to'] = $subsite_email;
        }

        return $notification;
    }
}

// Initialize the SubSiteContactForm class
new SubSiteContactForm(5, 1);
