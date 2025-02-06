<section id="logos" class="bg-white dark:bg-gray-900 hidden md:block">
    <div class="py-8 lg:py-16 mx-auto max-w-screen-xl px-4">
        <h2 class="mb-8 lg:mb-16 text-3xl font-extrabold tracking-tight leading-tight text-center text-gray-900 dark:text-white md:text-4xl">
            <?php echo get_field('logo_titel'); ?>
        </h2>
        <div class="grid grid-cols-2 lg:flex lg:flex-wrap justify-between items-center gap-8 text-gray-500 dark:text-gray-400">
            <?php if( have_rows('logos') ): ?>
                <?php while( have_rows('logos') ): the_row(); ?>
                    <a href="#" class="flex justify-center items-center">
                        <figure class="hover:text-gray-900 dark:hover:text-white">
                            <img class="!h-8 md:!h-12 w-auto" src="<?php echo get_sub_field('logo')['url']; ?>" alt="Logo">
                        </figure>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>