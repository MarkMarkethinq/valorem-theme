<section id="reviews" class="bg-primary-900">
    <div class="py-8 px-4 mx-auto max-w-screen-xl text-center lg:py-16 lg:px-6">
        <div class="mx-auto max-w-screen-sm mb-8 lg:mb-16">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-white"><?php echo get_field('titel'); ?></h2>
            <div class="font-light text-white sm:text-xl"><?php echo get_field('tekst'); ?></div>
        </div> 
        <div id="testimonial-carousel" class="relative" data-carousel="slide">
            <div class="overflow-x-hidden overflow-y-visible relative mx-auto max-w-screen-md h-52 rounded-lg sm:h-48">
                <?php if (have_rows('review')): ?>
                  <?php while (have_rows('review')): the_row(); ?>
                <figure class="hidden mx-auto w-full max-w-screen-md" data-carousel-item>
                    <blockquote>
                        <div class="text-lg font-medium text-white sm:text-2xl"><?php echo get_sub_field('review_tekst'); ?></div>
                    </blockquote>
                    <figcaption class="flex justify-center items-center mt-6 space-x-3">
                        <?php if (get_sub_field('bedrijfslogo')): ?>
                            <img class="w-6 h-6 rounded-full object-cover" src="<?php echo get_sub_field('bedrijfslogo')['url']; ?>" alt="<?php echo get_sub_field('bedrijfslogo')['alt']; ?>">
                        <?php else: ?>
                            <img class="w-6 h-6 rounded-full object-cover" src="<?php echo get_stylesheet_directory_uri(); ?>/includes/placeholder-image/person-placeholder.jpg" alt="Placeholder logo">
                        <?php endif; ?>
                        <div class="pr-3 font-medium text-white"><?php echo get_sub_field('bedrijfsnaam'); ?></div>
                    </figcaption>
                </figure>
                <?php endwhile; ?>
                <?php endif; ?>
            </div>
            <div class="flex justify-center items-center">
                <button type="button" class="flex justify-center items-center mr-4 h-full cursor-pointer group focus:outline-none" data-carousel-prev>
                    <span class="text-white ">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg>
                        <span class="hidden">Previous</span>
                    </span>
                </button>
                <button type="button" class="flex justify-center items-center h-full cursor-pointer group focus:outline-none" data-carousel-next>
                    <span class="text-white ">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        <span class="hidden">Next</span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    </section>