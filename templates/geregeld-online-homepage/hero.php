<section id="hero" class="bg-white antialiased">
  <div class="w-full max-w-screen-xl px-4 py-14 mx-auto lg:px-6 sm:py-16 lg:py-24">
    <div class="flex flex-col items-center gap-8 xl:gap-16 lg:flex-row">
      <div class="text-center md:max-w-2xl lg:!text-left xl:shrink-0">
        <div>
          <h1 class="text-4xl font-extrabold leading-tight tracking-tight text-gray-900 sm:!text-6xl pb-4">
            <?php echo get_field('hero_titel'); ?>
          </h1>
          <div class="mt-4 !text-xl font-normal text-gray-800 md:max-w-3xl md:mx-auto sm:text-2xl">
            <?php echo wpautop(get_field('hero_content')); ?>
          </div>
        </div>

        <div class="flex items-center justify-center gap-4 mt-8 lg:!justify-start">
          <?php $button_links = get_field('hero_button_links'); 
          if(!empty($button_links['title'])):
          ?>
          <a href="<?php echo $button_links['url']; ?>" title="<?php echo $button_links['title']; ?>"
            class="px-5 py-3 text-base font-medium text-center text-white bg-primary-500 rounded-lg shrink-0 hover:bg-primary-600 focus:ring-4 focus:outline-none focus:ring-primary-300"
            role="button">
            <?php echo $button_links['title']; ?>
          </a>
          <?php endif; ?>

          <!-- <?php $button_rechts = get_field('hero_button_rechts'); 
          if(!empty($button_rechts['title'])):
          ?>
          <a href="<?php echo $button_rechts['url']; ?>" target="<?php echo $button_rechts['target']; ?>" title="<?php echo $button_rechts['title']; ?>"
            class="inline-flex items-center px-5 py-3 text-base font-medium text-center text-secondary bg-white border border-secondary rounded-lg shrink-0 focus:outline-none hover:bg-secondary hover:text-white focus:z-10 focus:ring-4 focus:ring-gray-200 group"
            role="button">
            <?php echo $button_rechts['title']; ?>
            <svg 
                class="w-5 h-5 ml-2 -mr-1 fill-secondary group-hover:fill-white"
                viewBox="-23 -21 682 682.66669" 
                xmlns="http://www.w3.org/2000/svg" 
                id="fi_1384023">
                <path d="m544.386719 93.007812c-59.875-59.945312-139.503907-92.9726558-224.335938-93.007812-174.804687 0-317.070312 142.261719-317.140625 317.113281-.023437 55.894531 14.578125 110.457031 42.332032 158.550781l-44.992188 164.335938 168.121094-44.101562c46.324218 25.269531 98.476562 38.585937 151.550781 38.601562h.132813c174.785156 0 317.066406-142.273438 317.132812-317.132812.035156-84.742188-32.921875-164.417969-92.800781-224.359376zm-224.335938 487.933594h-.109375c-47.296875-.019531-93.683594-12.730468-134.160156-36.742187l-9.621094-5.714844-99.765625 26.171875 26.628907-97.269531-6.269532-9.972657c-26.386718-41.96875-40.320312-90.476562-40.296875-140.28125.054688-145.332031 118.304688-263.570312 263.699219-263.570312 70.40625.023438 136.589844 27.476562 186.355469 77.300781s77.15625 116.050781 77.132812 186.484375c-.0625 145.34375-118.304687 263.59375-263.59375 263.59375zm144.585938-197.417968c-7.921875-3.96875-46.882813-23.132813-54.148438-25.78125-7.257812-2.644532-12.546875-3.960938-17.824219 3.96875-5.285156 7.929687-20.46875 25.78125-25.09375 31.066406-4.625 5.289062-9.242187 5.953125-17.167968 1.984375-7.925782-3.964844-33.457032-12.335938-63.726563-39.332031-23.554687-21.011719-39.457031-46.960938-44.082031-54.890626-4.617188-7.9375-.039062-11.8125 3.476562-16.171874 8.578126-10.652344 17.167969-21.820313 19.808594-27.105469 2.644532-5.289063 1.320313-9.917969-.664062-13.882813-1.976563-3.964844-17.824219-42.96875-24.425782-58.839844-6.4375-15.445312-12.964843-13.359374-17.832031-13.601562-4.617187-.230469-9.902343-.277344-15.1875-.277344-5.28125 0-13.867187 1.980469-21.132812 9.917969-7.261719 7.933594-27.730469 27.101563-27.730469 66.105469s28.394531 76.683594 32.355469 81.972656c3.960937 5.289062 55.878906 85.328125 135.367187 119.648438 18.90625 8.171874 33.664063 13.042968 45.175782 16.695312 18.984374 6.03125 36.253906 5.179688 49.910156 3.140625 15.226562-2.277344 46.878906-19.171875 53.488281-37.679687 6.601563-18.511719 6.601563-34.375 4.617187-37.683594-1.976562-3.304688-7.261718-5.285156-15.183593-9.253906zm0 0" 
                fill-rule="evenodd">
                </path>
            </svg>
          </a>
          <?php endif; ?> -->
        </div>

        <div class="flex flex-col md:flex-row items-center justify-center gap-5 mt-8 lg:!justify-start sm:gap-6">
          <div class="overflow-hidden">
            <img class="inline-block object-contain w-auto h-12 border border-white rounded-full"
              src="<?php echo get_field('hero_review_img')['url']; ?>" alt="">
          </div>

          <div class="hidden md:block w-px h-8 bg-gray-200"></div>

          <div>
            <div class="flex items-center justify-center gap-1.5">
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
              <span class="hidden md:block text-base font-normal text-gray-500">
                Gemiddelde beoordeling: 4,8
              </span>
              <span class="block md:hidden text-base font-normal text-gray-500">
                4,8
              </span>
            </div>
            <p class="mt-1 !text-sm font-normal text-gray-500">
              <?php echo get_field('hero_review_tekst'); ?>
            </p>
          </div>
        </div>
      </div>

      <div class="max-w-lg hidden md:block">
        <img class="object-contain w-auto" src="<?php echo get_field('hero_afbeelding')['url']; ?>" alt="<?php echo get_field('hero_afbeelding')['alt']; ?>">
      </div>
    </div>
  </div>
</section>