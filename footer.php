<?php
/**
 * The template for displaying the footer
 */
?>

</main>

<?php 
// Include feedback tool for logged-in users
if (is_user_logged_in()) {
    //get_template_part('includes/feedback-tool/templates/feedback-form');
}
?>

<?php wp_footer(); ?>

</body>
</html>