<?php
// Get the assigned page template
$current_template = get_page_template_slug();

// Strip ".php" to get the template directory name
$template_name = basename( $current_template, '.php' );

// Define the directory for this template
$template_parts_directory = 'templates/' . $template_name . '/';

// Define part orders for each template
$template_part_orders = [
    'schilder' => [ 'header', 'hero', 'content', 'features', 'projects', 'afbeelding-carousel', 'about', 'contact', 'footer' ],
    'geregeld-online-homepage' => [ 'header', 'hero', 'logos', 'content', 'steps', 'kosten', 'reviews', 'faq', 'contact', 'footer' ],
    'geregeld-online-intake' => [ 'header', 'registration', 'footer' ],
    'geregeld-online-oud' => [ 'header', 'home', 'footer' ]
];

// Determine the order for the current template
$part_order = $template_part_orders[ $template_name ] ?? [];

// Check if the directory exists
if ( is_dir( trailingslashit( get_template_directory() ) . $template_parts_directory ) ) {

    // Dynamically load parts in the specified order
    function autoload_template_parts( $directory, $order ) {
        $base_path = trailingslashit( get_template_directory() ) . $directory;

        // Check if the directory exists
        if ( ! is_dir( $base_path ) ) {
            echo "Error: Directory $base_path not found!";
            return;
        }

        // Load files in the defined order
        foreach ( $order as $filename ) {
            $file_path = $base_path . $filename . '.php';
            if ( file_exists( $file_path ) ) {
                include $file_path;
            } else {
                echo "<!-- Missing part: $filename -->";
            }
        }
    }

    // Load the parts in the order for this template
    autoload_template_parts( $template_parts_directory, $part_order );

} else {
    // Fallback if no custom directory is found
    echo '<p>Template directory for "' . esc_html( $template_name ) . '" not found.</p>';
    get_template_part( 'template-parts/content', 'none' );
}
