<section id="over-mij" class="bg-primary-900">
    <div class="gap-16 items-center py-8 px-4 mx-auto max-w-screen-xl lg:grid lg:grid-cols-12 lg:py-16 lg:px-6">
        <div class="font-light text-white text-2xl lg:col-span-7">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-white"><?php echo get_field('titel') ?></h2>
            <p class="mb-4"><?php echo get_field('tekst') ?></p>
            <?php if (get_field('knop')): ?>
            <a href="<?php echo get_field('knop')['url']; ?>" target="<?php echo get_field('knop')['target']; ?>" class="inline-flex text-base items-center py-3 mt-8 mb-6 px-5 font-medium text-center border border-gray-300 text-white rounded-lg hover:bg-white hover:text-black focus:ring-4 focus:outline-none focus:ring-primary-900">
                <?php echo get_field('knop')['title']; ?>
                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </a>
            <?php endif; ?>
        </div>
        <div class="grid grid-cols-2 gap-4  lg:col-span-5">
            <?php if (get_field('afbeelding_links')): ?>
            <img class="mx-auto w-3/4 h-[500px] md:w-2/3 md:h-[600px] lg:w-full lg:h-full lg:max-h-[700px] object-cover rounded-lg col-span-2" src="<?php echo get_field('afbeelding_links')['url'] ?>" alt="<?php echo get_field('afbeelding_links')['alt'] ?>">
            <?php endif; ?>
        </div>
    </div>
</section>