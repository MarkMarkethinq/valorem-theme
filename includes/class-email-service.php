<?php

namespace Includes;

use Includes\Logger;

/**
 * Class Email_Service
 * 
 * A flexible email service for sending HTML emails with dynamic content,
 * compatible with Post SMTP plugin.
 */
class Email_Service {
    /**
     * @var string The path to the email templates directory
     */
    private $template_dir;

    /**
     * @var bool Whether to use mock mode (no actual emails sent)
     */
    private $mock_mode = false;

    /**
     * @var string Default template to use
     */
    private $default_template = 'default';

    /**
     * Initialize the email service
     * 
     * @param string $template_dir Path to email templates directory
     */
    public function __construct($template_dir = null) {
        $this->template_dir = $template_dir ?: get_template_directory() . '/includes/email-templates';

        // Create templates directory if it doesn't exist
        if (!file_exists($this->template_dir)) {
            mkdir($this->template_dir, 0755, true);
        }
    }

    /**
     * Enable or disable mock mode
     * 
     * @param bool $enabled Whether to enable mock mode
     */
    public function set_mock_mode($enabled = true) {
        $this->mock_mode = $enabled;
    }

    /**
     * Set the default template to use
     * 
     * @param string $template Template name
     */
    public function set_default_template($template) {
        $this->default_template = $template;
    }

    /**
     * Send an email
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param array $data Dynamic data for template
     * @param string $template Template to use (optional)
     * @return bool Whether the email was sent successfully
     */
    public function send_email($to, $subject, $data, $template = null) {
        $template = $template ?: $this->default_template;
        $html_content = $this->prepare_email_content($template, $data);

        if ($this->mock_mode) {
            Logger::info('Mock email would be sent', [
                'to' => $to,
                'subject' => $subject,
                'data' => $data,
                'template' => $template
            ], 'email_service');
            return true;
        }

        // Set up headers for HTML email
        $headers = [
            'Content-Type: text/html; charset=UTF-8'
        ];

        // Let Post SMTP handle the email sending
        $sent = wp_mail($to, $subject, $html_content, $headers);

        if ($sent) {
            Logger::info('Email sent successfully', [
                'to' => $to,
                'subject' => $subject,
                'template' => $template
            ], 'email_service');
        } else {
            Logger::error('Failed to send email', [
                'to' => $to,
                'subject' => $subject,
                'template' => $template
            ], 'email_service');
        }

        return $sent;
    }

    /**
     * Prepare email content by replacing placeholders in template
     * 
     * @param string $template Template name
     * @param array $data Dynamic data
     * @return string Prepared HTML content
     */
    private function prepare_email_content($template, $data) {
        $template_file = $this->template_dir . '/' . $template . '.php';
        
        if (!file_exists($template_file)) {
            Logger::error('Email template not found', [
                'template' => $template,
                'template_file' => $template_file
            ], 'email_service');
            return '';
        }

        // Start output buffering
        ob_start();
        
        // Extract data to make variables available in template
        extract($data);
        
        // Include template
        include $template_file;
        
        // Get content and clean buffer
        $content = ob_get_clean();

        // Replace any remaining placeholders
        foreach ($data as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return $content;
    }
} 