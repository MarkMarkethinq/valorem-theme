<!DOCTYPE html>
<html class="no-js"dir="ltr" <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php wp_head() ?>
    <!--
      ____                 _             _             
    |  _ \  _____   _____| | ___  _ __ (_)_ __   __ _ 
    | | | |/ _ \ \ / / _ \ |/ _ \| '_ \| | '_ \ / _` |
    | |_| |  __/\ V /  __/ | (_) | |_) | | | | | (_| |
    |____/ \___| \_/ \___|_|\___/| .__/|_|_| |_|\__, |
                                  |_|            |___/ 
		-->
        
  </head>
  <body <?php body_class(); ?>>

  <header>
    <nav class="bg-white border-gray-200 px-4 lg:px-6 py-2.5">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
            <a href="/" class="flex items-center">
                <?php if(has_custom_logo()): ?>
                    <div class="h-12 flex items-center">
                        <?php 
                        $custom_logo_id = get_theme_mod('custom_logo');
                        $logo = wp_get_attachment_image($custom_logo_id, 'full', false, array(
                            'class' => 'h-12 w-auto object-contain',
                        ));
                        echo $logo;
                        ?>
                    </div>
                <?php else: ?>
                    <span class="text-xl font-semibold"><?php bloginfo('name'); ?></span>
                <?php endif; ?>
            </a>
            <div class="flex items-center lg:order-2">
                <a href="#contact" class="text-white bg-primary-900 hover:bg-primary-950 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 lg:px-5 py-2 lg:py-2.5 mr-2 focus:outline-none">Stuur een bericht</a>
                <button data-collapse-toggle="mobile-menu-2" type="button" class="inline-flex items-center p-2 ml-1 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200" aria-controls="mobile-menu-2" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                    <svg class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <div class="hidden justify-between items-center pt-2 w-full lg:flex lg:w-auto lg:order-1" id="mobile-menu-2">
                <?php wp_nav_menu ([
                        'menu' => 'main',
                        'container' => 'ul',
                        'menu_class' => 'flex lg:gap-8 gap-4 lg:flex-row flex-col py-2 pr-4 pl-3 lg:p-0',
                        'walker' => new WPDocs_Walker_Nav_Menu ()
                    ]);  ?>
            </div>
        </div>
    </nav>
</header>