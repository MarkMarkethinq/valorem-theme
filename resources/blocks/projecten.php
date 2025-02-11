<section id="projecten" class="bg-white">
  <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
      <div class="mx-auto max-w-screen-sm text-center mb-8 lg:mb-16">
          <h2 class="mb-4 text-3xl lg:text-4xl tracking-tight font-extrabold text-gray-900"><?php echo get_field('titel'); ?></h2>
          <div class="font-light text-gray-500 text-xl lg:text-2xl"><?php echo get_field('tekst'); ?></div>
      </div> 
      <div class="projecten-carousel relative mx-4 lg:mx-12">
        <?php if (have_rows('projecten')): ?>
          <?php while (have_rows('projecten')): the_row(); ?>
          <div class="h-full px-2">
            <article class="flex flex-col h-full p-4 bg-white rounded-lg border border-gray-200">
               <?php if (get_sub_field('uitgelichte_afbeelding')): ?> 
                <figure class="w-full h-48 md:h-56 lg:h-64 mb-4 flex-shrink-0">
                    <img class="w-full h-full object-cover rounded-lg" src="<?php echo get_sub_field('uitgelichte_afbeelding')['url']; ?>" alt="<?php echo get_sub_field('uitgelichte_afbeelding')['alt']; ?>">
                </figure>
                <?php endif; ?>
                <div class="flex flex-col flex-1 justify-between">
                    <div>
                        <div class="mb-2">
                            <span class="inline-block bg-blue-100 text-blue-900 text-sm md:text-base font-semibold mr-2 px-2.5 py-0.5 rounded"><?php echo get_sub_field('categorie'); ?></span>
                        </div>
                        <h2 class="mb-3 text-xl md:text-2xl font-bold tracking-tight text-gray-900">
                            <?php echo get_sub_field('project_titel'); ?>
                        </h2>
                        <div class="text-base md:text-lg font-light text-gray-500"><?php echo get_sub_field('beschrijving'); ?></div>
                    </div>
                </div>
            </article>
          </div>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>  
  </div>
</section>