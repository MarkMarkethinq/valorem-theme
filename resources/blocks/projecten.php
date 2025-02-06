<section id="projecten" class="bg-white">
  <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
      <div class="mx-auto max-w-screen-sm text-center mb-8 lg:mb-16">
          <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900"><?php echo get_field('titel'); ?></h2>
          <div class="font-light text-gray-500 sm:text-xl"><?php echo get_field('tekst'); ?></div>
      </div> 
      <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        <?php if (have_rows('projecten')): ?>
          <?php while (have_rows('projecten')): the_row(); ?>
          <article class="p-4 bg-white rounded-lg border border-gray-200 shadow-md">
             <?php if (get_sub_field('uitgelichte_afbeelding')): ?> 
              <figure>
                  <img class="mb-5 rounded-lg" src="<?php echo get_sub_field('uitgelichte_afbeelding')['url']; ?>" alt="<?php echo get_sub_field('uitgelichte_afbeelding')['alt']; ?>">
              </figure>
              <?php endif; ?>
              <span class="bg-purple-100 text-purple-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded"><?php echo get_sub_field('categorie'); ?></span>
              <h2 class="my-2 text-2xl font-bold tracking-tight text-gray-900">
                  <?php echo get_sub_field('project_titel'); ?>
              </h2>
              <div class="mb-4 font-light text-gray-500"><?php echo get_sub_field('beschrijving'); ?></div>
          </article>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>  
  </div>
</section>