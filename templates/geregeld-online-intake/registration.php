<section class="bg-white dark:bg-gray-900">
  <div class="grid lg:h-screen lg:grid-cols-2">
      <!-- Gravity Form Section -->
      <div class="flex flex-col justify-center items-center py-6 px-4 lg:py-0 sm:px-0">
          <!-- Gravity Form Shortcode -->
          <div class="w-full max-w-md">
              <?php echo do_shortcode(get_field('intake_shortcode')); ?>
          </div>
      </div>

      <div class="flex justify-center items-center py-6 px-4 bg-primary-600 lg:py-0 sm:px-0">
          <div class="max-w-md xl:!max-w-xl">
              <!-- <a href="#" class="flex items-center mb-4 text-2xl font-semibold text-white">
                  <svg class="mr-2 h-8" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                  </svg>
                  Flowbite
              </a> -->
              <h1 class="mb-4 text-3xl font-extrabold tracking-tight leading-none !text-white xl:text-4xl">
                  <?php echo get_field('intake_titel'); ?>
              </h1>
              <div class="mb-4 font-light text-primary-200 lg:mb-8">
                  <?php echo wpautop(get_field('intake_tekst')); ?>
              </div>
              
              <div class="flex items-center justify-center gap-5 mt-8 lg:justify-start sm:gap-6">
                <div class="overflow-hidden">
                    <img class="inline-block object-contain w-auto h-12" src="<?php echo get_field('intake_review_img')['url']; ?>" alt="">
                </div>

                <div class="w-px h-8 bg-gray-200 dark:bg-gray-700"></div>

                <div>
                    <div class="flex items-center gap-1.5">
                    <div class="flex items-center gap-0.5">
                        <svg aria-hidden="true" class="w-5 h-5 text-yellow-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <svg aria-hidden="true" class="w-5 h-5 text-yellow-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <svg aria-hidden="true" class="w-5 h-5 text-yellow-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <svg aria-hidden="true" class="w-5 h-5 text-yellow-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <svg aria-hidden="true" class="w-5 h-5 text-yellow-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <span class="hidden md:block text-base font-normal text-white">
                        Gemiddelde beoordeling: 4,8
                    </span>
                    <span class="block md:hidden text-base font-normal text-white">
                        4,8
                    </span>
                    </div>
                    <p class="mt-1 text-sm font-normal text-white">
                        <?php echo get_field('intake_review_tekst'); ?>
                    </p>
                </div>
                </div>
          </div>
      </div>
  </div>
</section>
