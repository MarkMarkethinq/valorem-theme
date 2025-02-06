<?php
$field_content_title = get_field('content_title');
$field_content_description = get_field('content_description');
$field_content_button = get_field('content_button');
$field_content_image_left = get_field('image_left');
$field_content_image_right = get_field('image_right');

?>
<section id="overmij" class="bg-white dark:bg-gray-900">
    <div class="gap-16 items-center py-8 px-4 mx-auto max-w-screen-2xl lg:grid lg:grid-cols-2 lg:py-16 lg:px-6">
        <div class="font-light text-gray-500 sm:text-lg dark:text-gray-400">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white"><?php echo esc_html($field_content_title) ?></h2>
            <div class="mb-4 text-gray-500 font-normal"><?php echo ($field_content_description) ?></div>
            <?php if ($field_content_button): ?>
                <a href="<?php echo ($field_content_button['url']) ?>" class="inline-flex items-center text-gray-900  py-3 px-5 font-medium text-center  rounded-lg border  ">
                    <?php echo ($field_content_button['title']) ?>
                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-8">
            <img class="w-full rounded-lg" src="<?php echo $field_content_image_left ?>" alt="office content 1">
            <img class="mt-4 w-full lg:mt-10 rounded-lg" src="<?php echo $field_content_image_right ?>" alt="office content 2">
        </div>
    </div>
</section>