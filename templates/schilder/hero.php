<?php
$field_hero_title = get_field('hero_title');
$field_hero_description = get_field('hero_description');
$field_hero_button = get_field('hero_button');
$field_hero_image = get_field('hero_image');
?>
<section id="hero" style="background-image: url('<?php echo $field_hero_image ?>')" class="bg-no-repeat bg-cover bg-center bg-gray-700 bg-blend-multiply ">
    <div class="flex flex-col justify-center items-center py-8 px-4 mx-auto max-w-screen-xl text-white lg:py-16 z-1">
        <div class="mb-6 text-center max-w-screen-lg py-28 lg:mb-0">
            <h1 class="mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl"><?php echo ($field_hero_title) ?></h1>
            <div class="mb-6 font-light  text-white lg:mb-8 md:text-lg lg:text-xl"><?php echo ($field_hero_description) ?></div>
            <?php if (get_field('hero_button')) : ?>
                <?php $button = get_field('hero_button') ?>
                <a href="<?php echo $button['url'] ?>" class="inline-flex items-center  bg- py-3 px-5 font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-900 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                <?php echo $button['title'] ?>
                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>