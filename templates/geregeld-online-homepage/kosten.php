<section id="kosten" class="bg-white dark:bg-gray-900">
  <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
      <div class="mx-auto max-w-screen-md text-center mb-8 lg:mb-12">
          <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white"><?php echo get_field('prijs_titel'); ?></h2>
          <div class="mb-5 text-lg font-medium text-gray-500 sm:text-xl dark:text-gray-400"><?php echo wpautop(get_field('prijs_tekst')); ?></div>
      </div>
      <div class="grid gap-8 xl:grid-cols-3 xl:gap-10">
          <div></div>
          <!-- Pricing Card -->
          <div class="flex flex-col p-6 mx-auto w-full text-center bg-white rounded-lg border shadow border-primary-600 dark:bg-gray-800 xl:p-8">
              <div class="mb-2">
                  <span class="py-1 px-3 text-sm text-secondary bg-amber-100 rounded"><?php echo get_field('prijs_badge'); ?></span>
              </div>
              <h3 class="mb-4 text-2xl font-medium text-gray-900 dark:text-white"><?php echo get_field('plan_titel'); ?></h3>
              <span class="text-5xl font-extrabold text-gray-900 dark:text-white"><?php echo get_field('plan_prijs'); ?></span>
              <p class="mt-4 mb-1 text-gray-500 text-light dark:text-gray-400"><?php echo get_field('plan_interval'); ?></p>
              <!-- <a href="#" class="inline-flex justify-center items-center font-medium text-primary-600 hover:text-primary-800 dark:text-primary-500 dark:hover:text-primary-700">
                  Go to annual plan
                  <svg class="ml-2 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
              </a> -->
              <?php if( get_field('plan_knop') ): ?>
                <a href="<?php echo get_field('plan_knop')['url']; ?>" class="text-white bg-primary-500 hover:bg-primary-600 focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 font-medium rounded-lg text-sm px-5 py-2.5 text-center my-8"><?php echo get_field('plan_knop')['title']; ?></a>
              <?php endif; ?>
              <!-- List -->
              <?php if( have_rows('plan_voordelen') ): ?>
                  <ul role="list" class="space-y-4">
                  <?php while( have_rows('plan_voordelen') ): the_row(); ?>
                        <li class="flex space-x-2.5">
                            <svg class="flex-shrink-0 w-5 h-5 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span class="font-normal text-base leading-tight text-gray-500 dark:text-gray-400"><?php echo get_sub_field('plan_voordeel'); ?></span>
                        </li>
                  <?php endwhile; ?>
                  </ul>
              <?php endif; ?>
          </div>
          <div></div>
      </div>
  </div>
</section>