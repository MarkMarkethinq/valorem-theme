<?php get_footer(); ?>

<footer class="bg-gradient-to-b from-transparent to-gray-100 py-12 dark:to-gray-900">
  <div class="mx-auto flex max-w-6xl flex-col items-center px-6 md:px-12 lg:px-6 xl:px-0">
    <a href="#home" aria-label="logo" class="flex items-center justify-center space-x-2">
        <img class="mr-3 h-10" src="<?php echo esc_url(wp_get_attachment_url(get_theme_mod('custom_logo'))); ?>">
    </a>

    <ul role="list" class="mt-8 flex flex-wrap items-center justify-center gap-4 py-4 text-gray-600 dark:text-gray-400 sm:gap-8">
        <div class="pt-0 justify-between text-base font-medium tracking-wide items-center w-full flex lg:w-auto lg:order-1" id="mobile-menu-2">

		</div>
    </ul>

    <!-- <div class="m-auto mt-8 flex flex-col w-max items-center gap-0 space-x-4 text-gray-500">
      <h2 class="mb-8 text-2xl text-gray-700 text-center dark:text-gray-100">Houd mij op de hoogte</h2>
      <?php echo do_shortcode('[gravityform id="2" title="false" description="false" ajax="true"]'); ?>
    </div> -->

    <div class="mt-8 text-center flex flex-col">
      <span class="text-sm font-thin tracking-wide text-gray-500 mb-2"><?php echo "Copyright © geregeld online " . date("Y") . " | All rights reserved"; ?></span>
      <span class="text-sm font-thin tracking-wide text-gray-500">Onderdeel van <a href="https://developing.nl/" target="_blank" class="underline">Developing</a> | kvk: 71112405</span>
    </div>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>