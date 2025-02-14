<section style="background-image: url('<?php echo get_field('afbeelding')['url']; ?>');" class=" bg-no-repeat bg-cover bg-center bg-gray-700 bg-blend-multiply">
  <div class="relative py-16 px-4 mx-auto max-w-screen-xl text-white text-center lg:py-32 xl:px-0 z-1">
      <div class="mb-6 mx-auto max-w-screen-md lg:mb-0">
          <h1 class="mb-4 text-4xl md:text-4xl lg:text-6xl font-extrabold tracking-tight leading-tight text-white"><?php echo get_field('titel'); ?></h1>
          <div class="mb-6 font-light text-gray-300 text-2xl"><?php echo get_field('tekst'); ?></div>
          <?php if( get_field('knop') ): ?>
          <a href="<?php echo get_field('knop')['url']; ?>" class="inline-flex items-center py-3 px-5 font-medium text-center text-white rounded-lg bg-primary-900 hover:bg-primary-950 focus:ring-4 focus:outline-none focus:ring-primary-900">
              <?php echo get_field('knop')['title']; ?>
          </a>
          <?php endif; ?>
      </div> 
  </div>
</section>
