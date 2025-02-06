<?php
/**
 * Template Name: Feedback Overzicht
 * 
 * Template voor het weergeven van alle feedback van alle subsites
 */

get_header();
?>

<div class="container mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-8">Feedback Overzicht</h1>

    <?php
    if (!is_multisite() || !current_user_can('manage_network')) {
        echo '<p class="text-red-600">Je hebt geen toegang tot dit overzicht.</p>';
        get_footer();
        return;
    }

    // Get all sites
    $sites = get_sites();
    
    // Initialize total counts
    $total_pending = 0;
    $total_in_progress = 0;
    $total_completed = 0;
    $total_rejected = 0;
    ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-blue-100 p-4 rounded-lg">
            <h3 class="font-semibold text-blue-800">In afwachting</h3>
            <span class="text-2xl text-blue-600" id="count-pending">0</span>
        </div>
        <div class="bg-yellow-100 p-4 rounded-lg">
            <h3 class="font-semibold text-yellow-800">In behandeling</h3>
            <span class="text-2xl text-yellow-600" id="count-in-progress">0</span>
        </div>
        <div class="bg-green-100 p-4 rounded-lg">
            <h3 class="font-semibold text-green-800">Afgerond</h3>
            <span class="text-2xl text-green-600" id="count-completed">0</span>
        </div>
        <div class="bg-red-100 p-4 rounded-lg">
            <h3 class="font-semibold text-red-800">Afgewezen</h3>
            <span class="text-2xl text-red-600" id="count-rejected">0</span>
        </div>
    </div>

    <div class="space-y-8">
        <?php foreach ($sites as $site): 
            switch_to_blog($site->blog_id);
            
            // Get feedback for this site
            $args = array(
                'post_type' => 'feedback',
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key' => 'feedback_status',
                        'value' => array('pending', 'in-progress'),
                        'compare' => 'IN'
                    )
                )
            );
            
            $feedback_query = new WP_Query($args);
            
            if ($feedback_query->have_posts()): ?>
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">
                        <a href="<?php echo get_site_url(); ?>" target="_blank" class="text-blue-600 hover:underline">
                            <?php echo get_bloginfo('name'); ?>
                        </a>
                    </h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Element</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feedback</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php while ($feedback_query->have_posts()): $feedback_query->the_post();
                                    $status = get_post_meta(get_the_ID(), 'feedback_status', true);
                                    $element_type = get_post_meta(get_the_ID(), 'element_type', true);
                                    $feedback_type = get_post_meta(get_the_ID(), 'feedback_type', true);
                                    
                                    // Update counters
                                    switch ($status) {
                                        case 'pending': $total_pending++; break;
                                        case 'in-progress': $total_in_progress++; break;
                                        case 'completed': $total_completed++; break;
                                        case 'rejected': $total_rejected++; break;
                                    }
                                    
                                    $status_classes = array(
                                        'pending' => 'bg-blue-100 text-blue-800',
                                        'in-progress' => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800'
                                    );
                                    ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo esc_html($feedback_type); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo esc_html($element_type); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?php echo wp_trim_words(get_the_content(), 10); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_classes[$status]; ?>">
                                                <?php echo esc_html(ucfirst($status)); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo get_the_date('j M Y H:i'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="<?php echo get_edit_post_link(); ?>" target="_blank" class="text-blue-600 hover:text-blue-900">
                                                Bewerken
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif;
            
            wp_reset_postdata();
            restore_current_blog();
        endforeach; ?>
    </div>

    <script>
    // Update counters
    document.getElementById('count-pending').textContent = '<?php echo $total_pending; ?>';
    document.getElementById('count-in-progress').textContent = '<?php echo $total_in_progress; ?>';
    document.getElementById('count-completed').textContent = '<?php echo $total_completed; ?>';
    document.getElementById('count-rejected').textContent = '<?php echo $total_rejected; ?>';
    </script>
</div>

<?php get_footer(); ?> 