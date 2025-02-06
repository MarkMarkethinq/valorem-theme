<?php
/**
 * Class IntakeForm
 *
 * Handles specific tasks for the intake form, including script enabling and confirmation anchor.
 */
class IntakeForm {

    /**
     * Constructor.
     */
    public function __construct() {
        // Register hooks
        add_filter('gform_enqueue_scripts', [$this, 'enqueueScripts']);
        add_filter('gform_confirmation_anchor', [$this, 'disableConfirmationAnchor']);
    }

    /**
     * Enable Gravity Forms scripts.
     *
     * @return bool Always returns true.
     */
    public function enqueueScripts() {
        return true;
    }

    /**
     * Disable confirmation anchor.
     *
     * @return bool Always returns false.
     */
    public function disableConfirmationAnchor() {
        return false;
    }
}

// Initialize the IntakeForm class
new IntakeForm();