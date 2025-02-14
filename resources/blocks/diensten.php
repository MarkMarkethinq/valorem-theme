<section id="diensten" class="bg-white">
  <div class="gap-8 items-center pb-8 max-lg:pt-16 px-4 mx-auto max-w-screen-xl lg:grid lg:grid-cols-2 xl:gap-16 lg:pb-16 lg:pt-[8rem] lg:px-6 ">
    <?php if (get_field('grote_afbeelding')): ?>
      <img class="mb-4 w-full h-full object-cover lg:mb-0 rounded-lg" src="<?php echo get_field('grote_afbeelding')['url'] ?>" alt="<?php echo get_field('grote_afbeelding')['alt'] ?>">
    <?php endif; ?>
      <div class="text-gray-500 text-2xl">
          <h2 class="mb-4 max-lg:text-3xl lg:text-4xl tracking-tight font-extrabold text-gray-900 "><?php echo get_field('titel') ?></h2>
          <div class="mb-8 font-light "><?php echo get_field('tekst') ?></div>
          <div class="py-8 mb-6 border-t border-b border-gray-200 ">
              <?php if(have_rows('diensten')): ?>
                <?php while(have_rows('diensten')): the_row(); ?>
                  <div class="flex pt-8">
                      <div class="flex justify-center items-center mr-4 w-8 h-8 rounded-full bg-primary-100 shrink-0">
                          <?php if(get_sub_field('icoon')): ?>
                            <img src="<?php echo get_sub_field('icoon')['url']; ?>" alt="<?php echo get_sub_field('icoon')['alt']; ?>" class="w-5 h-5">
                          <?php endif; ?>
                      </div>
                      <div>
                          <h3 class="mb-2 text-2xl font-bold text-gray-900 "><?php echo get_sub_field('titel'); ?></h3>
                          <p class="mb-2 font-light text-xl text-gray-500 "><?php echo get_sub_field('tekst'); ?></p>
                      </div>
                  </div>
                <?php endwhile; ?>
              <?php endif; ?>
          </div>
          <p class="text-sm"><?php echo get_field('tekst_onder') ?></p>
      </div>
  </div>
</section>