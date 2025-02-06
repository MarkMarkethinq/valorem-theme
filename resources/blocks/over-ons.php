<section id="over-ons" class="bg-white dark:bg-gray-900">
    <div class="gap-16 items-center py-8 px-4 mx-auto max-w-screen-xl lg:grid lg:grid-cols-2 lg:py-16 lg:px-6">
        <div class="font-light text-gray-500 sm:text-lg dark:text-gray-400">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white"><?php echo get_field('titel') ?></h2>
            <p class="mb-4"><?php echo get_field('tekst') ?></p>
            <?php if (get_field('knop')): ?>
            <a href="<?php echo get_field('knop')['url']; ?>" target="<?php echo get_field('knop')['target']; ?>" class="inline-flex items-center py-3 mt-4 px-5 font-medium text-center border border-black text-grey-700 rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-900 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                <?php echo get_field('knop')['title']; ?>
                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </a>
            <?php endif; ?>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-8">
            <?php if (get_field('afbeelding_links')): ?>
            <img class="w-full rounded-lg" src="<?php echo get_field('afbeelding_links')['url'] ?>" alt="<?php echo get_field('afbeelding_links')['alt'] ?>">
            <?php endif; ?>
            <?php if (get_field('afbeelding_rechts')): ?>
            <img class="mt-4 w-full lg:mt-10 rounded-lg" src="<?php echo get_field('afbeelding_rechts')['url'] ?>" alt="<?php echo get_field('afbeelding_rechts')['alt'] ?>">
            <?php endif; ?>
        </div>
    </div>
</section>