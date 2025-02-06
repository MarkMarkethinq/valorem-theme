<?php 

/* Register custom gutenberg blocks */
add_action('acf/init', 'anna_init_block_types');
function anna_init_block_types() {
    /* Check if acf_register_block_type function exists. */
    if( function_exists('acf_register_block_type') ) {
      /* Set gutenberg block names */
      $custom_blocks = array(
        'Projecten',
        'Reviews',
        'Contact',
        'Hero'
      );
      /* Create gutenberg blocks args */
      foreach ($custom_blocks as $block_key => $title) {
        $slug = sanitize_title($title);
        $args = array(
            'name'              => sanitize_title($slug),
            'title'             => $title,
            'description'       => $title.' LP Block',
            'mode' => 'edit',
            'render_template'   => 'resources/blocks/'.$slug.'.php',
            'category'          => 'theme-blocks',
            'icon'              => 'block-default',
            'keywords'          => array($slug, $title)
        );
        // Enqueue gutenberg block style (backend & fronted)
        /*$css_file_helper = str_replace('/app/_acf_blocks.php', '', __FILE__).'/public';

        if(file_exists($css_file_helper.'/blocks/css/'.$slug.'.css')){
          $args['enqueue_style'] = LP_PUBLIC.'/blocks/css/'.$slug.'.css';
        }
        // Enqueue gutenberg block javascript (backend & fronted)
        if(file_exists($css_file_helper.'/blocks/js/'.$slug.'.js')){
          $args['enqueue_script'] = LP_PUBLIC.'/blocks/js/'.$slug.'.js';
        }*/
        //Register gutenberg blocks
        acf_register_block_type($args);
      }
    }
}