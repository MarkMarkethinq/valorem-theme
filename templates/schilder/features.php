<?php
$field_features_card_title = get_field('features_card_title');
$field_features_card_description = get_field('features_card_description');
$field_features_card_contact_button = get_field('contact_button');
?>




<section id="features" class="bg-white dark:bg-gray-900">
    <div class="items-center py-8 px-4 mx-auto max-w-screen-2xl lg:grid xl:grid-cols-3 lg:gap-8 xl:gap-24 sm:py-16 lg:px-6">
        <div class="mb-8 lg:mb-0">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white"><?php echo ($field_features_card_title) ?></h2>
            <div class="mb-4 text-gray-500 sm:text-xl dark:text-gray-400"><?php echo ($field_features_card_description) ?></div>
            <?php if ($field_features_card_contact_button): ?>
                <a href="<?php echo ($field_features_card_contact_button['url']) ?>" class="inline-flex items-center text-lg font-medium text-primary-600 hover:text-primary-800 dark:text-primary-500 dark:hover:text-primary-700">
                    <?php echo ($field_features_card_contact_button['title']) ?>
                    <svg class="ml-1 w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            <?php endif; ?>
            <?php if (get_field('keurmerk')): ?>
                <figure class="flex items-center my-4">
                    <img class="h-8 sm:h-12 w-auto" src="<?php echo get_field('keurmerk')['url']; ?>" alt="">
                </figure>
            <?php endif; ?>
        </div>
        <div class="col-span-2 space-y-8 md:grid md:grid-cols-2 md:gap-12 md:space-y-0">
            <?php if (have_rows('features')): ?>
                <div class="col-span-2 space-y-8 md:grid md:grid-cols-2 md:gap-12 md:space-y-0">
                    <?php while (have_rows('features')): the_row(); ?>
                        <?php
                        // Fetch the repeater sub-fields
                        $title = get_sub_field('title');
                        $description = get_sub_field('description');
                        ?>
                        <div>
                            <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-lg bg-primary-100 lg:h-12 lg:w-12 dark:bg-primary-900">
                            <svg width="24" height="20" viewBox="0 0 24 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.58649 20C8.19479 20.0019 7.81804 19.8179 7.5368 19.4874L0.452928 11.1549C0.311734 10.9878 0.199073 10.7887 0.121378 10.5688C0.0436829 10.3489 0.00247545 10.1127 0.000108269 9.87348C-0.00467247 9.39044 0.149042 8.92488 0.427436 8.57922C0.705829 8.23356 1.0861 8.03612 1.48459 8.03032C1.88308 8.02453 2.26714 8.21086 2.5523 8.54832L8.59249 15.6502L21.4466 0.517612C21.7322 0.180149 22.1166 -0.00598898 22.5154 0.00014703C22.9142 0.00628304 23.2946 0.204191 23.573 0.550332C23.8514 0.896473 24.0049 1.36249 23.9999 1.84587C23.9948 2.32926 23.8316 2.7904 23.546 3.12787L9.63617 19.4874C9.35494 19.8179 8.97818 20.0019 8.58649 20Z" fill="#1A56DB"/>
                            </svg>
                            </div>
                            <h3 class="mb-2 text-xl font-bold dark:text-white">
                                <?php echo esc_html($title); ?>
                            </h3>
                            <div class="text-gray-500 dark:text-gray-400">
                                <?php echo ($description); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>There are no features available at this time.</p>
            <?php endif; ?>
        </div>
    </div>
</section>