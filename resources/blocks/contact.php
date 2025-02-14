<div id="contact" class="relative isolate bg-white">
  <div class="mx-auto grid max-w-7xl grid-cols-1 lg:grid-cols-2">
    <div class="relative px-6 pb-20 pt-24 sm:pt-32 lg:static lg:px-8 lg:py-48">
      <div class="mx-auto max-w-xl lg:mx-0 lg:max-w-lg">
        <!-- <div class="absolute inset-y-0 left-0 -z-10 w-full overflow-hidden bg-gray-100 ring-1 ring-gray-900/10 lg:w-1/2">
          <svg class="absolute inset-0 size-full stroke-gray-200 [mask-image:radial-gradient(100%_100%_at_top_right,white,transparent)]" aria-hidden="true">
            <defs>
              <pattern id="83fd4e5a-9d52-42fc-97b6-718e5d7ee527" width="200" height="200" x="100%" y="-1" patternUnits="userSpaceOnUse">
                <path d="M130 200V.5M.5 .5H200" fill="none" />
              </pattern>
            </defs>
            <rect width="100%" height="100%" stroke-width="0" fill="white" />
            <svg x="100%" y="-1" class="overflow-visible fill-gray-50">
              <path d="M-470.5 0h201v201h-201Z" stroke-width="0" />
            </svg>
            <rect width="100%" height="100%" stroke-width="0" fill="url(#83fd4e5a-9d52-42fc-97b6-718e5d7ee527)" />
          </svg>
        </div> -->
        <h2 class="text-pretty max-lg:text-3xl lg:text-4xl font-semibold tracking-tight text-gray-900"><?php echo get_field('titel'); ?></h2>
        <div class="mt-6 font-light text-2xl text-gray-500"><?php echo get_field('tekst'); ?></div>
        <dl class="mt-10 space-y-8 font-light text-xl text-gray-500">
        <?php if( have_rows('info') ): ?>
          <?php while( have_rows('info') ): the_row(); ?>
          <div class="flex gap-x-4">
            <dt class="flex-none">
              <span class="sr-only">Address</span>
              <figure class="h-7 w-6 text-gray-400">
                  <img src="<?php echo get_sub_field('icoon')['url']; ?>" alt="<?php echo get_sub_field('icoon')['alt']; ?>">
              </figure>
            </dt>
            <dd><?php echo get_sub_field('tekst'); ?></dd>
          </div>
          <?php endwhile; ?>
        <?php endif; ?>
        </dl>
      </div>
    </div>
    <div class="my-auto max-w-3xl px-6">
        <?php echo do_shortcode(get_field('formulier_shortcode')); ?>
    </div>
  </div>
</div>
