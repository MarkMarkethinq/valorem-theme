<?php
/**
 * Plugin Name: Feedback Support Tool
 * Description: Een interactieve support tool met chatbot functionaliteit
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: feedback-support-tool
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Require Composer's autoloader
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

// Register autoloader for our classes
spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'FeedbackSupportTool\\';

    // Base directory for the namespace prefix
    $base_dir = plugin_dir_path(__FILE__) . 'src/';

    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize the plugin
function init_feedback_support_tool() {
    $tool = new FeedbackSupportTool\FeedbackSupportTool();
    $tool->init();
}
add_action('init', 'init_feedback_support_tool'); 