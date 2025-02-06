<section id="wat-is-het" class="bg-white dark:bg-gray-900">
    <div class="gap-16 items-center py-8 px-4 mx-auto max-w-screen-xl lg:grid lg:grid-cols-2 lg:py-16 lg:px-6">
        <div class="font-light text-center md:!text-left text-gray-500 sm:!text-lg dark:text-gray-400">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white"><?php echo get_field('content_titel'); ?></h2>
            <div class="mb-4 text-lg font-normal text-gray-500"><?php echo wpautop(get_field('content_tekst')); ?></div>
            <?php $button = get_field('content_button'); 
            if(!empty($button['title'])):
            ?>
            <a href="<?php echo $button['url']; ?>" title="<?php echo $button['title']; ?>"
                class="inline-flex items-center px-5 py-3 text-sm font-medium text-center text-white bg-secondary border border-secondary rounded-lg shrink-0 focus:outline-none hover:bg-secondaryHover hover:text-white focus:z-10 focus:ring-4 focus:ring-gray-200"
                role="button">
                <?php echo $button['title']; ?>
                <svg aria-hidden="true" class="w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
                </svg>
            </a>
            <?php endif; ?>
        </div>
        <div class="mt-8">
            <?php if(get_field('content_afbeelding')): ?>
                <img class="w-full rounded-lg" src="<?php echo get_field('content_afbeelding')['url']; ?>" alt="<?php echo get_field('content_afbeelding')['alt']; ?>">
            <?php endif; ?>
        </div>
    </div>
</section>