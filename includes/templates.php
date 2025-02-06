<?php
// Universal Template Handling
add_filter( 'page_template', function ( $page_template ) {
    if ( is_page() && get_page_template_slug() ) {
        return locate_template( 'templates/handler.php' );
    }
    return $page_template;
});

// Register custom page templates
add_filter( 'theme_page_templates', function ( $templates ) {
    $templates['templates/schilder.php'] = 'Schilder Template';
    $templates['templates/geregeld-online-homepage.php'] = 'Geregeld Online - Homepage';
    $templates['templates/geregeld-online-intake.php'] = 'Geregeld Online - Intake';
    $templates['templates/geregeld-online-oud.php'] = 'Geregeld Online - Oud';
    return $templates;
});
