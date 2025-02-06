<?php


require_once __DIR__ . '/vendor/autoload.php';
require_once 'acf_blocks.php';



// Load all necessary includes
$includes = [
    'includes/templates.php',
    'includes/theme-setup.php',
];

foreach ($includes as $file) {
    if (file_exists(get_template_directory() . '/' . $file)) {
        require_once get_template_directory() . '/' . $file;
    }
}


// Flowbite en popup scripts laden
function enqueue_popup_files() {
    // Flowbite CSS en JS
    wp_enqueue_style('flowbite', 'https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css');
    wp_enqueue_script('flowbite', 'https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_popup_files');

