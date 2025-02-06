<?php
/**
 * Feedback Tool
 *
 * @package GoTheme
 */

class Feedback_Tool {
    /**
     * Version number for assets
     */
    private $version = '1.0.0';

    /**
     * Constructor
     */
    public function __construct() {
        // Constructor is called on instantiation
    }

    /**
     * Initialize the tool
     */
    public function init() {
        // Only load for logged-in users
        if (!is_user_logged_in()) {
            return;
        }

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Register AJAX handlers
        add_action('wp_ajax_submit_feedback', array($this, 'handle_feedback_submission'));
        add_action('wp_ajax_delete_feedback', array($this, 'handle_feedback_deletion'));
        add_action('wp_ajax_complete_feedback', array($this, 'handle_feedback_completion'));

        // Add admin customizations
        if (is_admin()) {
            add_filter('manage_feedback_posts_columns', array($this, 'add_feedback_columns'));
            add_action('manage_feedback_posts_custom_column', array($this, 'render_feedback_columns'), 10, 2);
            add_filter('manage_edit-feedback_sortable_columns', array($this, 'sortable_feedback_columns'));
            add_action('add_meta_boxes', array($this, 'add_feedback_meta_boxes'));
            add_action('save_post_feedback', array($this, 'save_feedback_meta'));

            // Add network admin customizations
            if (is_multisite()) {
                add_filter('wpmu_blogs_columns', array($this, 'add_feedback_count_column'));
                add_action('manage_sites_custom_column', array($this, 'render_feedback_count_column'), 10, 2);
                add_action('admin_head-sites.php', array($this, 'add_feedback_count_styles'));
                add_action('admin_head-site-info.php', array($this, 'add_feedback_count_styles'));
                
                // Add network admin menu
                add_action('network_admin_menu', array($this, 'add_network_admin_menu'));
            }
        }
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // jQuery UI
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('jquery-ui', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');

        // Feedback Tool CSS
        wp_enqueue_style(
            'feedback-tool',
            get_template_directory_uri() . '/includes/feedback-tool/assets/css/feedback-tool.css',
            array(),
            $this->version
        );

        // Feedback Tool JavaScript
        wp_enqueue_script(
            'feedback-tool',
            get_template_directory_uri() . '/includes/feedback-tool/assets/js/feedback-tool.js',
            array('jquery', 'jquery-ui-dialog'),
            $this->version,
            true
        );

        // Localize script
        wp_localize_script(
            'feedback-tool',
            'feedbackToolSettings',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('feedback_tool_nonce'),
                'strings' => array(
                    'success' => __('Feedback succesvol verzonden!', 'go-theme'),
                    'error' => __('Er is een fout opgetreden bij het versturen van de feedback.', 'go-theme')
                ),
                'feedback' => $this->get_user_feedback()
            )
        );
    }

    /**
     * Handle feedback submission
     */
    public function handle_feedback_submission() {
        // Verify nonce
        if (!check_ajax_referer('feedback_tool_nonce', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
            return;
        }

        $feedback_data = json_decode(stripslashes($_POST['feedback']), true);
        
        // Create feedback post
        $post_data = array(
            'post_title'   => sprintf('Feedback: %s - %s', $feedback_data['elementType'], $feedback_data['feedbackType']),
            'post_content' => $feedback_data['comment'],
            'post_status'  => 'publish',
            'post_type'    => 'feedback'
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            wp_send_json_error('Kon feedback niet opslaan');
            return;
        }

        // Save feedback metadata
        update_post_meta($post_id, 'feedback_type', $feedback_data['feedbackType']);
        update_post_meta($post_id, 'element_type', $feedback_data['elementType']);
        update_post_meta($post_id, 'element_id', $feedback_data['elementId']);
        update_post_meta($post_id, 'element_classes', $feedback_data['elementClasses']);
        update_post_meta($post_id, 'element_selector', $feedback_data['elementSelector']);
        update_post_meta($post_id, 'feedback_status', 'in-progress');

        // Handle file uploads
        if (!empty($_FILES['attachments']['name'][0])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $attachment_ids = array();
            $files = $_FILES['attachments'];
            
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = array(
                        'name'     => $files['name'][$key],
                        'type'     => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error'    => $files['error'][$key],
                        'size'     => $files['size'][$key]
                    );
                    
                    $_FILES = array('upload' => $file);
                    $attachment_id = media_handle_upload('upload', $post_id);

                    if (!is_wp_error($attachment_id)) {
                        array_push($attachment_ids, $attachment_id);
                    }
                }
            }

            if (!empty($attachment_ids)) {
                update_post_meta($post_id, 'feedback_attachments', $attachment_ids);
            }
        }

        wp_send_json_success(array(
            'id' => $post_id,
            'message' => 'Feedback succesvol opgeslagen'
        ));
    }

    /**
     * Handle feedback deletion
     */
    public function handle_feedback_deletion() {
        // Verify nonce
        if (!check_ajax_referer('feedback_tool_nonce', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }

        // Check user permissions
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
        }

        // Get and validate data
        $feedback_id = isset($_POST['feedback_id']) ? intval($_POST['feedback_id']) : 0;
        if (!$feedback_id) {
            wp_send_json_error('Invalid feedback ID');
        }

        // Verify ownership
        $post = get_post($feedback_id);
        if (!$post || $post->post_author != get_current_user_id()) {
            wp_send_json_error('Unauthorized');
        }

        // Delete feedback
        if (wp_delete_post($feedback_id, true)) {
            wp_send_json_success(array(
                'message' => __('Feedback succesvol verwijderd', 'go-theme')
            ));
        } else {
            wp_send_json_error(__('Kon feedback niet verwijderen', 'go-theme'));
        }

        die();
    }

    /**
     * Add custom columns to feedback list
     */
    public function add_feedback_columns($columns) {
        $new_columns = array();
        
        // Add checkbox
        $new_columns['cb'] = $columns['cb'];
        
        // Add other columns
        $new_columns['title'] = $columns['title'];
        $new_columns['element_type'] = __('Element Type', 'go-theme');
        $new_columns['element_selector'] = __('Element Selector', 'go-theme');
        $new_columns['feedback_type'] = __('Feedback Type', 'go-theme');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }

    /**
     * Render custom column content
     */
    public function render_feedback_columns($column, $post_id) {
        switch ($column) {
            case 'element_type':
                echo esc_html(get_post_meta($post_id, 'element_type', true));
                break;
            
            case 'element_selector':
                $selector = get_post_meta($post_id, 'element_selector', true);
                if (empty($selector)) {
                    $id = get_post_meta($post_id, 'element_id', true);
                    $classes = get_post_meta($post_id, 'element_classes', true);
                    $selector = $id ? "#$id" : ".$classes";
                }
                echo esc_html($selector);
                break;
            
            case 'feedback_type':
                echo esc_html(get_post_meta($post_id, 'feedback_type', true));
                break;
        }
    }

    /**
     * Make custom columns sortable
     */
    public function sortable_feedback_columns($columns) {
        $columns['element_type'] = 'element_type';
        $columns['feedback_type'] = 'feedback_type';
        return $columns;
    }

    /**
     * Get feedback for current user
     * 
     * @return array
     */
    private function get_user_feedback() {
        $args = array(
            'post_type' => 'feedback',
            'posts_per_page' => -1,
            'author' => get_current_user_id(),
            'orderby' => 'date',
            'order' => 'DESC'
        );

        $posts = get_posts($args);
        $feedback = array();

        foreach ($posts as $post) {
            $feedback[] = array(
                'id' => $post->ID,
                'date' => get_the_date('j F Y H:i', $post->ID),
                'elementType' => get_post_meta($post->ID, 'element_type', true),
                'elementSelector' => get_post_meta($post->ID, 'element_selector', true),
                'feedbackType' => get_post_meta($post->ID, 'feedback_type', true),
                'comment' => $post->post_content,
                'status' => get_post_meta($post->ID, 'feedback_status', true) ?: 'pending',
                'response' => get_post_meta($post->ID, 'feedback_response', true)
            );
        }

        return $feedback;
    }

    /**
     * Add meta boxes for feedback status and attachments
     */
    public function add_feedback_meta_boxes() {
        add_meta_box(
            'feedback_status',
            __('Feedback Status', 'go-theme'),
            array($this, 'render_status_meta_box'),
            'feedback',
            'side',
            'high'
        );

        add_meta_box(
            'feedback_attachments',
            __('Bijlagen', 'go-theme'),
            array($this, 'render_attachments_meta_box'),
            'feedback',
            'normal',
            'high'
        );
    }

    /**
     * Render status meta box
     */
    public function render_status_meta_box($post) {
        $status = get_post_meta($post->ID, 'feedback_status', true) ?: 'in-progress';
        wp_nonce_field('feedback_status', 'feedback_status_nonce');
        ?>
        <select name="feedback_status" style="width: 100%;">
            <option value="in-progress" <?php selected($status, 'in-progress'); ?>>Mee bezig</option>
            <option value="completed" <?php selected($status, 'completed'); ?>>Afgerond</option>
            <option value="rejected" <?php selected($status, 'rejected'); ?>>Afgewezen</option>
        </select>
        <?php
    }

    /**
     * Render attachments meta box
     */
    public function render_attachments_meta_box($post) {
        $attachment_ids = get_post_meta($post->ID, 'feedback_attachments', true);
        
        if (empty($attachment_ids)) {
            echo '<p>Geen bijlagen toegevoegd.</p>';
            return;
        }

        echo '<div class="feedback-attachments">';
        foreach ($attachment_ids as $attachment_id) {
            $attachment = get_post($attachment_id);
            if (!$attachment) continue;

            $file_url = wp_get_attachment_url($attachment_id);
            $file_type = get_post_mime_type($attachment_id);
            $file_name = basename($file_url);
            $is_image = strpos($file_type, 'image') !== false;

            echo '<div class="feedback-attachment">';
            
            if ($is_image) {
                echo wp_get_attachment_image($attachment_id, 'thumbnail');
            } else {
                $icon = 'dashicons-media-default';
                if (strpos($file_type, 'pdf') !== false) {
                    $icon = 'dashicons-pdf';
                } elseif (strpos($file_type, 'word') !== false) {
                    $icon = 'dashicons-media-document';
                } elseif (strpos($file_type, 'text') !== false) {
                    $icon = 'dashicons-text';
                }
                echo '<span class="dashicons ' . esc_attr($icon) . '"></span>';
            }

            echo '<div class="attachment-info">';
            echo '<a href="' . esc_url($file_url) . '" target="_blank">' . esc_html($file_name) . '</a>';
            echo '<span class="file-type">' . esc_html($file_type) . '</span>';
            echo '</div>';
            
            echo '</div>';
        }
        echo '</div>';

        // Add some inline styles
        ?>
        <style>
            .feedback-attachments {
                display: grid;
                gap: 15px;
                padding: 10px 0;
            }
            .feedback-attachment {
                display: flex;
                align-items: center;
                padding: 10px;
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 4px;
            }
            .feedback-attachment img {
                width: 50px;
                height: 50px;
                object-fit: cover;
                margin-right: 15px;
            }
            .feedback-attachment .dashicons {
                font-size: 2em;
                width: 50px;
                height: 50px;
                margin-right: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .attachment-info {
                display: flex;
                flex-direction: column;
            }
            .attachment-info a {
                text-decoration: none;
                color: #007bff;
                margin-bottom: 5px;
            }
            .attachment-info .file-type {
                color: #6c757d;
                font-size: 0.9em;
            }
        </style>
        <?php
    }

    /**
     * Save feedback meta
     */
    public function save_feedback_meta($post_id) {
        if (!isset($_POST['feedback_status_nonce']) || !wp_verify_nonce($_POST['feedback_status_nonce'], 'feedback_status')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['feedback_status'])) {
            update_post_meta($post_id, 'feedback_status', sanitize_text_field($_POST['feedback_status']));
        }
    }

    /**
     * Add feedback count column to sites list
     */
    public function add_feedback_count_column($columns) {
        $columns['feedback_count'] = __('Feedback', 'go-theme');
        return $columns;
    }

    /**
     * Render feedback count column
     */
    public function render_feedback_count_column($column, $blog_id) {
        if ($column !== 'feedback_count') {
            return;
        }

        switch_to_blog($blog_id);
        
        // Get count of pending feedback
        $count = $this->get_pending_feedback_count();
        
        if ($count > 0) {
            echo '<span class="feedback-count">' . $count . '</span>';
        }

        restore_current_blog();
    }

    /**
     * Add styles for feedback count
     */
    public function add_feedback_count_styles() {
        ?>
        <style>
            .feedback-count {
                display: inline-block;
                background-color: #d63638;
                color: #fff;
                font-size: 11px;
                line-height: 1.2;
                font-weight: 600;
                padding: 0 5px;
                min-width: 18px;
                height: 18px;
                border-radius: 9px;
                text-align: center;
            }
            .column-feedback_count {
                width: 70px;
                text-align: center !important;
            }
        </style>
        <?php
    }

    /**
     * Get count of pending feedback
     */
    private function get_pending_feedback_count() {
        $args = array(
            'post_type' => 'feedback',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'feedback_status',
                    'value' => array('pending', 'in-progress'),
                    'compare' => 'IN'
                )
            ),
            'posts_per_page' => -1
        );

        $query = new WP_Query($args);
        return $query->found_posts;
    }

    /**
     * Add network admin menu
     */
    public function add_network_admin_menu() {
        add_menu_page(
            __('Feedback Overzicht', 'go-theme'),
            __('Feedback', 'go-theme'),
            'manage_network',
            'network-feedback',
            array($this, 'render_network_feedback_page'),
            'dashicons-feedback',
            25
        );
    }

    /**
     * Render network feedback page
     */
    public function render_network_feedback_page() {
        // Handle email notification submission
        if (isset($_POST['send_notification']) && check_admin_referer('send_feedback_notification')) {
            $this->send_feedback_notification($_POST);
        }

        // Initialize total counts
        $total_pending = 0;
        $total_in_progress = 0;
        $total_completed = 0;
        $total_rejected = 0;

        // Get all sites
        $sites = get_sites();
        
        // Collect all feedback items
        $all_feedback = array();
        foreach ($sites as $site) {
            switch_to_blog($site->blog_id);
            
            $args = array(
                'post_type' => 'feedback',
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key' => 'feedback_status',
                        'value' => array('pending', 'in-progress', 'completed', 'rejected'),
                        'compare' => 'IN'
                    )
                )
            );
            
            $feedback_query = new WP_Query($args);
            
            if ($feedback_query->have_posts()) {
                while ($feedback_query->have_posts()) {
                    $feedback_query->the_post();
                    $status = get_post_meta(get_the_ID(), 'feedback_status', true);
                    
                    // Update counters
                    switch ($status) {
                        case 'pending': $total_pending++; break;
                        case 'in-progress': $total_in_progress++; break;
                        case 'completed': $total_completed++; break;
                        case 'rejected': $total_rejected++; break;
                    }
                    
                    $all_feedback[] = array(
                        'id' => get_the_ID(),
                        'site_id' => $site->blog_id,
                        'site_name' => get_bloginfo('name'),
                        'site_url' => get_site_url(),
                        'type' => get_post_meta(get_the_ID(), 'feedback_type', true),
                        'element_type' => get_post_meta(get_the_ID(), 'element_type', true),
                        'content' => get_the_content(),
                        'status' => $status,
                        'date' => get_the_date('Y-m-d H:i:s'),
                        'edit_link' => get_edit_post_link()
                    );
                }
            }
            
            wp_reset_postdata();
            restore_current_blog();
        }
        
        // Sort feedback by date (newest first)
        usort($all_feedback, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e('Feedback Overzicht', 'go-theme'); ?></h1>

            <div class="feedback-stats four-col" style="margin: 20px 0; display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                <div class="stat-box" style="background: #f0f6fc; padding: 20px; border-radius: 4px; border-left: 4px solid #2271b1;">
                    <h3 style="margin: 0 0 10px 0; color: #2271b1;">In afwachting</h3>
                    <span style="font-size: 24px; font-weight: 600; color: #2271b1;" id="count-pending"><?php echo $total_pending; ?></span>
                </div>
                <div class="stat-box" style="background: #fcf9e8; padding: 20px; border-radius: 4px; border-left: 4px solid #dba617;">
                    <h3 style="margin: 0 0 10px 0; color: #dba617;">In behandeling</h3>
                    <span style="font-size: 24px; font-weight: 600; color: #dba617;" id="count-in-progress"><?php echo $total_in_progress; ?></span>
                </div>
                <div class="stat-box" style="background: #f0f6eb; padding: 20px; border-radius: 4px; border-left: 4px solid #00a32a;">
                    <h3 style="margin: 0 0 10px 0; color: #00a32a;">Afgerond</h3>
                    <span style="font-size: 24px; font-weight: 600; color: #00a32a;" id="count-completed"><?php echo $total_completed; ?></span>
                </div>
                <div class="stat-box" style="background: #fcf0f1; padding: 20px; border-radius: 4px; border-left: 4px solid #d63638;">
                    <h3 style="margin: 0 0 10px 0; color: #d63638;">Afgewezen</h3>
                    <span style="font-size: 24px; font-weight: 600; color: #d63638;" id="count-rejected"><?php echo $total_rejected; ?></span>
                </div>
            </div>

            <div class="tablenav top">
                <!-- Hier kunnen we later filtering en pagination toevoegen -->
            </div>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col">Website</th>
                        <th scope="col">Type</th>
                        <th scope="col">Element</th>
                        <th scope="col">Feedback</th>
                        <th scope="col">Status</th>
                        <th scope="col">Datum</th>
                        <th scope="col">Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $status_colors = array(
                        'pending' => '#2271b1',
                        'in-progress' => '#dba617',
                        'completed' => '#00a32a',
                        'rejected' => '#d63638'
                    );

                    foreach ($all_feedback as $feedback): ?>
                        <tr<?php echo $feedback['status'] === 'completed' ? ' style="opacity: 0.7;"' : ''; ?>>
                            <td>
                                <a href="<?php echo esc_url($feedback['site_url']); ?>" target="_blank">
                                    <?php echo esc_html($feedback['site_name']); ?>
                                </a>
                            </td>
                            <td><?php echo esc_html($feedback['type']); ?></td>
                            <td><?php echo esc_html($feedback['element_type']); ?></td>
                            <td><?php echo wp_trim_words($feedback['content'], 10); ?></td>
                            <td>
                                <span class="feedback-status" style="display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 12px; line-height: 1.2; background: <?php echo esc_attr($status_colors[$feedback['status']]); ?>1a; color: <?php echo esc_attr($status_colors[$feedback['status']]); ?>;">
                                    <?php echo esc_html(ucfirst($feedback['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('j M Y H:i', strtotime($feedback['date'])); ?></td>
                            <td>
                                <label class="complete-feedback-label" style="margin-right: 10px;">
                                    <input type="checkbox" 
                                           class="complete-feedback" 
                                           data-id="<?php echo esc_attr($feedback['id']); ?>"
                                           data-site="<?php echo esc_attr($feedback['site_id']); ?>"
                                           <?php checked($feedback['status'] === 'completed'); ?>>
                                    Afgerond
                                </label>
                                <a href="<?php echo esc_url($feedback['edit_link']); ?>" class="button button-small">
                                    Bewerken
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="tablenav bottom">
                <!-- Hier kunnen we later filtering en pagination toevoegen -->
            </div>
        </div>

        <div class="wrap" style="margin-top: 40px;">
            <h2><?php _e('Feedback Notificatie Versturen', 'go-theme'); ?></h2>
            <p class="description" style="margin-bottom: 20px;">
                Gebruik dit formulier om klanten te informeren over verwerkte feedback.
            </p>

            <div class="card" style="padding: 20px;">
                <form method="post" action="">
                    <?php wp_nonce_field('send_feedback_notification'); ?>
                    
                    <table class="form-table" style="width: 100%;">
                        <tr>
                            <th scope="row" style="width: 200px;"><label for="notification_site">Website</label></th>
                            <td>
                                <select name="site_id" id="notification_site" class="regular-text" style="width: 100%; max-width: none;" required>
                                    <option value="">Selecteer website...</option>
                                    <?php 
                                    foreach (get_sites() as $site) {
                                        switch_to_blog($site->blog_id);
                                        echo sprintf(
                                            '<option value="%d">%s</option>',
                                            $site->blog_id,
                                            get_bloginfo('name')
                                        );
                                        restore_current_blog();
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="notification_email">E-mailadres</label></th>
                            <td>
                                <input type="email" name="notification_email" id="notification_email" class="regular-text" style="width: 100%;" required>
                                <p class="description">Het e-mailadres van de ontvanger</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="notification_subject">Onderwerp</label></th>
                            <td>
                                <input type="text" name="notification_subject" id="notification_subject" class="regular-text" 
                                    value="Feedback verwerkt" style="width: 100%;" required>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="notification_message">Bericht</label></th>
                            <td>
                                <textarea name="notification_message" id="notification_message" rows="8" style="width: 100%;" required>Beste,

We hebben uw feedback verwerkt en de gewenste aanpassingen doorgevoerd.

Met vriendelijke groet,
<?php echo get_bloginfo('name'); ?></textarea>
                            </td>
                        </tr>
                    </table>

                    <div style="margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 4px;">
                        <input type="submit" name="send_notification" class="button button-primary button-large" value="Verstuur Notificatie">
                    </div>
                </form>
            </div>
        </div>
        <?php

        // Add JavaScript for handling completion
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('.complete-feedback').on('change', function() {
                const $checkbox = $(this);
                const feedbackId = $checkbox.data('id');
                const siteId = $checkbox.data('site');
                const completed = $checkbox.prop('checked');

                // Disable checkbox during request
                $checkbox.prop('disabled', true);

                // Update status label
                const $statusLabel = $checkbox.closest('tr').find('.feedback-status');
                const $row = $checkbox.closest('tr');

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'complete_feedback',
                        nonce: '<?php echo wp_create_nonce('feedback_tool_nonce'); ?>',
                        feedback_id: feedbackId,
                        site_id: siteId,
                        completed: completed
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update status colors and text
                            if (completed) {
                                $statusLabel
                                    .css({
                                        'background-color': '#00a32a1a',
                                        'color': '#00a32a'
                                    })
                                    .text('Afgerond');
                                $row.css('opacity', '0.7');
                            } else {
                                $statusLabel
                                    .css({
                                        'background-color': '#dba6171a',
                                        'color': '#dba617'
                                    })
                                    .text('In behandeling');
                                $row.css('opacity', '1');
                            }
                        } else {
                            // Revert checkbox on error
                            $checkbox.prop('checked', !completed);
                            alert('Er is een fout opgetreden bij het bijwerken van de status.');
                        }
                    },
                    error: function() {
                        // Revert checkbox on error
                        $checkbox.prop('checked', !completed);
                        alert('Er is een fout opgetreden bij het bijwerken van de status.');
                    },
                    complete: function() {
                        // Re-enable checkbox
                        $checkbox.prop('disabled', false);
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Send feedback notification email
     */
    private function send_feedback_notification($data) {
        $site_id = intval($data['site_id']);
        $to = sanitize_email($data['notification_email']);
        $subject = sanitize_text_field($data['notification_subject']);
        $message = wp_kses_post($data['notification_message']);

        if (!$site_id || !is_email($to)) {
            add_settings_error(
                'feedback_notification',
                'invalid_data',
                'Ongeldige gegevens. Controleer het e-mailadres en de website.',
                'error'
            );
            return;
        }

        switch_to_blog($site_id);
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );

        $message = wpautop($message); // Convert newlines to paragraphs
        
        if (wp_mail($to, $subject, $message, $headers)) {
            add_settings_error(
                'feedback_notification',
                'mail_sent',
                'Notificatie is succesvol verzonden.',
                'success'
            );
        } else {
            add_settings_error(
                'feedback_notification',
                'mail_error',
                'Er is een fout opgetreden bij het verzenden van de notificatie.',
                'error'
            );
        }

        restore_current_blog();
    }

    /**
     * Handle feedback completion via AJAX
     */
    public function handle_feedback_completion() {
        // Verify nonce
        if (!check_admin_referer('feedback_tool_nonce', 'nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }

        $feedback_id = isset($_POST['feedback_id']) ? intval($_POST['feedback_id']) : 0;
        $site_id = isset($_POST['site_id']) ? intval($_POST['site_id']) : 0;
        $completed = isset($_POST['completed']) ? $_POST['completed'] === 'true' : false;

        if (!$feedback_id || !$site_id) {
            wp_send_json_error('Invalid data');
            return;
        }

        switch_to_blog($site_id);
        
        update_post_meta($feedback_id, 'feedback_status', $completed ? 'completed' : 'in-progress');
        
        restore_current_blog();

        wp_send_json_success(array(
            'message' => $completed ? 'Feedback gemarkeerd als afgerond' : 'Feedback gemarkeerd als in behandeling'
        ));
    }
}