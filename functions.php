<?php


// require_once __DIR__ . '/vendor/autoload.php';
require_once 'acf_blocks.php';



// Load all necessary includes
$includes = [
    'includes/theme-setup.php',
    'includes/enqueue-scripts.php',
    'includes/class-custom-walker.php'
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

register_nav_menus(array(
    'footer-menu' => __('Footer Menu', 'jouw-theme-naam'),
));

function theme_customize_register($wp_customize) {
    $wp_customize->add_section('contact_info', array(
        'title' => __('Contact Informatie', 'jouw-theme-naam'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('contact_email');
    $wp_customize->add_control('contact_email', array(
        'label' => __('Email Adres', 'jouw-theme-naam'),
        'section' => 'contact_info',
        'type' => 'text',
    ));

    $wp_customize->add_setting('contact_phone');
    $wp_customize->add_control('contact_phone', array(
        'label' => __('Telefoonnummer', 'jouw-theme-naam'),
        'section' => 'contact_info',
        'type' => 'text',
    ));
}
add_action('customize_register', 'theme_customize_register');

