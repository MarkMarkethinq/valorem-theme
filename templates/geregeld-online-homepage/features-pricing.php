<section id="kosten">
  <div class="gap-8 items-center py-8 px-4 mx-auto max-w-screen-xl lg:grid lg:grid-cols-2 xl:gap-16 lg:py-16 lg:px-6">
      <div class="text-gray-500 sm:text-lg">
          <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white"><?php echo get_field('prijs_titel'); ?></h2>
          <div class="mb-8 font-normal lg:text-lg dark:text-gray-400"><?php echo wpautop(get_field('prijs_tekst')); ?></div>
          <div class="grid gap-8 py-8 border-t border-gray-200 lg:grid-cols-1 dark:border-gray-700 sm:grid-cols-2">
              <?php if( have_rows('prijs_voordelen') ): ?>
                  <?php while( have_rows('prijs_voordelen') ): the_row(); ?>
                    <div class="flex">
                        <div class="flex justify-center items-center mr-4 w-12 h-12 bg-white rounded shadow shrink-0 dark:bg-gray-700">
                            <figure>
                                <img class="h-6 w-auto" src="<?php echo get_sub_field('voordelen_afbeelding')['url']; ?>" alt="Logo">
                            </figure>
                        </div>
                        <div>
                            <h3 class="mb-1 text-xl font-bold text-gray-900 dark:text-white"><?php echo get_sub_field('voordelen_titel'); ?></h3>
                            <div class="font-normal text-base text-gray-500 dark:text-gray-400"><?php echo wpautop(get_sub_field('voordelen_tekst')); ?></div>
                        </div>
                    </div>
                  <?php endwhile; ?>
              <?php endif; ?>
          </div>
      </div>
      <!-- Pricing Card -->
      <div class="flex flex-col p-6 bg-white rounded-lg shadow xl:p-8 dark:bg-gray-800">
          <div class="justify-between items-center md:flex">
              <div>
                  <div class="flex justify-between mb-2">
                      <h3 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo get_field('plan_titel'); ?></h3>
                      <div class="flex items-center md:hidden">
                          <div class="mr-1 text-xl font-extrabold text-gray-900 lg:text-5xl dark:text-white"><?php echo get_field('plan_prijs'); ?></div>
                          <span class="text-gray-500 dark:text-gray-400"><?php echo get_field('plan_interval'); ?></span>
                      </div>
                  </div>
                  <div class="text-lg font-normal text-gray-500 dark:text-gray-400 md:mr-2"><?php echo wpautop(get_field('plan_tekst')); ?></div>
              </div>
              <div class="hidden md:block">
                  <div class="text-2xl font-extrabold text-gray-900 lg:text-5xl dark:text-white"><?php echo get_field('plan_prijs'); ?></div>
                  <span class="text-base font-medium text-gray-500 dark:text-gray-400"><?php echo get_field('plan_interval'); ?></span>
              </div>
          </div>
          <a href="<?php echo get_field('plan_knop')['url']; ?>" class="text-white bg-primary-500 hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center my-5 lg:my-8 dark:focus:ring-primary-900"><?php echo get_field('plan_knop')['title']; ?></a>
          <div class="justify-between space-y-4 sm:space-y-0 sm:flex">
            <div class="grid grid-cols-2 gap-4">
              <?php if( have_rows('plan_voordelen') ): ?>
                  <?php 
                  $count = 0;
                  $total_rows = count(get_field('plan_voordelen')); 
                  $half = ceil($total_rows / 2);
                  ?>
                  <ul role="list" class="space-y-4">
                  <?php while( have_rows('plan_voordelen') ): the_row(); ?>
                        <?php if($count == $half): ?>
                          </ul><ul role="list" class="space-y-4">
                        <?php endif; ?>
                        <li class="flex space-x-2.5">
                            <svg class="flex-shrink-0 w-5 h-5 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span class="font-normal text-base leading-tight text-gray-500 dark:text-gray-400"><?php echo get_sub_field('plan_voordeel'); ?></span>
                        </li>
                        <?php $count++; ?>
                  <?php endwhile; ?>
                  </ul>
              <?php endif; ?>
            </div>
          </div>
      </div>
  </div>
</section>