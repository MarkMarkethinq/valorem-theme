<section id="hoe-het-werkt" class="bg-white dark:bg-gray-900">
  <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
      <div class="text-center text-gray-900">
          <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 lg:text-5xl dark:text-white"><?php echo get_field('steps_titel'); ?></h2>
          <div class="mb-4 text-lg font-medium text-gray-500"><?php echo wpautop(get_field('steps_tekst')); ?></div>
      </div>
      <div class="grid gap-6 mt-12 lg:mt-14 lg:gap-12 md:grid-cols-3 relative">
        <?php if(have_rows('stap')): ?>
            <?php 
            $count = 0;
            while(have_rows('stap')): the_row(); 
            $count++;
            ?>
            <div class="flex flex-row md:!flex-col mb-2 md:mb-0 relative">
                <?php if(get_sub_field('stap_afbeelding')): ?>
                    <div class="md:relative w-1/3 md:w-full my-auto md:my-0">
                        <div class="absolute -top-3 -left-3 w-12 h-12 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold z-20">
                            <?php echo str_pad($count, 2, '0', STR_PAD_LEFT); ?>
                        </div>
                        <?php 
                        $image = get_sub_field('stap_afbeelding');
                        $image_large = wp_get_attachment_image_src($image['ID'], 'large');
                        ?>
                        <img class="mr-4 w-36 h-36 md:w-full md:h-96 object-cover rounded-lg" src="<?php echo $image_large[0]; ?>" alt="<?php echo $image['alt']; ?>" />
                        <?php if($count < 3): ?>
                        <div class="hidden md:block absolute top-[50%] -right-[1.5rem] w-6 border-t-2 border-dashed border-gray-600 z-0"></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="w-2/3 md:w-full my-auto md:mt-4 ml-4 md:ml-0">
                    <h3 class="text-xl font-bold mb-2.5 text-gray-900 dark:text-white"><?php echo get_sub_field('stap_titel'); ?></h3>
                    <div class="text-base font-normal text-gray-500"><?php echo get_sub_field('stap_tekst'); ?></div>
                </div>
            </div>
        <?php endwhile; endif; ?>
      </div>        
  </div>
</section>