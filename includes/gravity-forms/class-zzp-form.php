<?php
add_filter( 'gform_notification_10', 'change_notification_email', 10, 3 );
function change_notification_email( $notification, $form, $entry ) {
    // Haal de globale $post op
    global $post;

    // Check of $post beschikbaar is
    if ( isset( $post->post_author ) ) {
        // Haal de e-mail van de auteur van de huidige post op
        $author_email = get_the_author_meta( 'user_email', $post->post_author );

        // Controleer of er een geldig e-mailadres is en vervang de ontvanger
        if ( !empty( $author_email )) {
            $notification['to'] = $author_email;
        }
    }

    return $notification;
}