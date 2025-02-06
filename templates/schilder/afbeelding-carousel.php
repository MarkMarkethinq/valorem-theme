<div class="py-8 lg:py-24 mx-auto max-w-screen-2xl">  
<h3 class="mb-4 text-4xl font-bold text-center text-gray-900 dark:text-white">
    <?php echo get_field('carousel_titel'); ?>
</h3>
<div class="afbeelding-carousel">
        <?php if (have_rows('afbeelding_carousel')): ?>
            <?php while (have_rows('afbeelding_carousel')): the_row(); ?>
                <figure>
                    <img class="w-full h-[400px] object-cover rounded-lg" src="<?php echo get_sub_field('afbeelding')['url']; ?>" alt="">
                </figure>
            <?php endwhile; ?>
        <?php endif; ?>
        </div>
</div>