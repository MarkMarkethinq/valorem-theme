<?php
// Theme Setup
add_action( 'after_setup_theme', function () {
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'post-formats', [ 'aside', 'gallery', 'quote', 'image', 'video' ] );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'html5', [ 'style', 'script' ] );
    add_theme_support( 'widgets' );

    // Register widget area
    register_sidebar( [
        'name'          => __( 'Footer Widget', 'myfirsttheme' ),
        'id'            => 'footer-widget',
        'description'   => __( 'Widgets displayed in the footer', 'myfirsttheme' ),
        'before_widget' => '<div class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );
} );
