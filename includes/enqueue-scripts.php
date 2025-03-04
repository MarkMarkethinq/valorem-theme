<?php
add_action('wp_enqueue_scripts', function () {
    // Styles
    wp_enqueue_style('myfirsttheme-style', get_stylesheet_uri());
    wp_enqueue_style('tailwind', get_template_directory_uri() . '/assets/css/tailwind-output.css', [], '1.0');
    wp_enqueue_style('slick', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_style('slick-theme', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');
    
    // Scripts
    wp_enqueue_script('flowbite', get_template_directory_uri() . '/node_modules/flowbite/dist/flowbite.min.js', [], '1.0', true);
    wp_enqueue_script('slick', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), null, true);
    wp_enqueue_script('app-js', get_template_directory_uri() . '/assets/js/app.js', ['jquery', 'slick', 'flowbite'], '1.0', true);
});
