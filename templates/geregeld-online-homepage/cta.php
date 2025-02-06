<section id="cta" class="bg-slate-100 dark:bg-gray-900">
    <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
        <div class="mx-auto max-w-screen-sm text-center">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold leading-tight text-gray-900 dark:text-white"><?php echo get_field('cta_titel'); ?></h2>
            <div class="mb-6 font-light text-gray-500 dark:text-gray-400 md:text-lg"><?php echo wpautop(get_field('cta_tekst')); ?></div>
            <a href="<?php echo get_field('cta_knop')['url']?>" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800"><?php echo get_field('cta_knop')['title']?></a>
        </div>
    </div>
</section>