<section id="contact" class="bg-white">
  <div class="max-w-screen-xl px-4 pb-8 max-lg:pt-16 mx-auto lg:px-6  lg:pt-[8rem] lg:pb-24">
    <div class="grid grid-cols-1 gap-6 text-center sm:gap-16 sm:grid-cols-2 lg:grid-cols-3">
        <?php if( have_rows('info') ): ?>
            <?php while( have_rows('info') ): the_row(); ?>
                <div>
                    <figure class="inline-flex items-center justify-center p-4 w-16 h-16 mx-auto text-gray-500 bg-gray-100 rounded-full">
                        <img src="<?php echo get_sub_field('icoon')['url']; ?>" alt="<?php echo get_sub_field('icoon')['alt']; ?>">
                    </figure>
                    <div class="mt-4">
                        <h3 class="text-2xl font-bold text-gray-900"><?php echo get_sub_field('titel'); ?></h3>
                        <div class="mt-1 text-xl font-normal text-gray-500"> <?php echo get_sub_field('tekst'); ?></div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <div class="max-w-3xl mx-auto mt-8 lg:mt-24">
            <?php echo do_shortcode(get_field('formulier_shortcode')); ?>
    </div>
  </div>
</section>