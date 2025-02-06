<aside id="projecten" aria-label="Mijn projecten" class="py-8 lg:py-24 bg-white dark:bg-gray-900 antialiased">
    <div class="p-4 mx-auto max-w-screen-2xl">
        <div class="carousel">
            <?php if (have_rows('projects')): // Controleer of er projecten zijn in de repeater 
            ?>
                <?php while (have_rows('projects')): the_row();
                    // Haal de subvelden op
                    $project_img = get_sub_field('project_img');
                    $project_title = get_sub_field('project_title');
                    $project_subtitle = get_sub_field('project_subtitle');
                    $project_description = get_sub_field('project_description');
                ?>
                    <article class="max-w-xs h-auto bg-white shadow rounded-lg p-4">
                        <?php if ($project_img): ?>
                            <img src="<?php echo ($project_img); ?>" class="mb-5 w-full h-52 object-cover rounded-lg" alt="<?php echo esc_attr($project_title); ?>">
                        <?php endif; ?>

                        <?php if ($project_title): ?>
                            <h2 class="mb-2 min-h-[50px] text-xl font-bold leading-tight text-gray-900 dark:text-white">
                                <?php echo ($project_title); ?>
                            </h2>
                        <?php endif; ?>

                        <?php if ($project_subtitle): ?>
                            <h3 class="mb-2 text-lg font-medium leading-tight text-gray-500 dark:text-white">
                                <?php echo ($project_subtitle); ?>
                            </h3>
                        <?php endif; ?>

                        <?php if ($project_description): ?>
                            <div class="font-normal text-gray-500 dark:text-gray-400 break-words">
                                <?php echo ($project_description); ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-gray-500 dark:text-gray-400">Geen projecten gevonden.</p>
            <?php endif; ?>
        </div>
    </div>
</aside>