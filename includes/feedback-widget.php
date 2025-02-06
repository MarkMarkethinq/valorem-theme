<?php
add_action( 'widgets_init', function () {
    register_widget( 'Feedback_Widget' );
} );

class Feedback_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'feedback_widget',
            __( 'Feedback Widget', 'text_domain' ),
            [ 'description' => __( 'Widget for Feedback and Support', 'text_domain' ) ]
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        ?>
        <div class="feedback-widget">
            <div class="widget-header"><?php esc_html_e( 'Feedback & Support', 'text_domain' ); ?></div>
            <div class="widget-content" style="display: none;">
                <div class="buttons">
                    <button id="feedback-button"><?php esc_html_e( 'Feedback', 'text_domain' ); ?></button>
                    <button id="support-button"><?php esc_html_e( 'Support', 'text_domain' ); ?></button>
                </div>
                <div id="feedback-form" style="display: none;">
                    <?php echo do_shortcode( '[gravityform id="3"]' ); ?>
                </div>
                <div id="support-form" style="display: none;">
                    <?php echo do_shortcode( '[gravityform id="4"]' ); ?>
                </div>
            </div>
        </div>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        echo '<p>' . __( 'No settings available for this widget.', 'text_domain' ) . '</p>';
    }

    public function update( $new_instance, $old_instance ) {
        return $old_instance;
    }
}