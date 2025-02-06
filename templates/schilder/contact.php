<?php
$field_bedrijfnaam = get_field('bedrijfnaam');
$field_straat = get_field('straat');
$field_postcode = get_field('postcode');
$field_stad = get_field('stad');
$field_telefoonnummer = get_field('telefoonnummer');
$field_email = get_field('email');
?>
<section id="contact" class="bg-white dark:bg-gray-900 px-10">
  <div class="max-w-screen-2xl  px-4 py-8 mx-auto lg:px-6 sm:py-16 lg:pb-2 lg:pt-24">
    <div class="grid grid-cols-1 gap-6 items-center justify-center text-center sm:gap-16 sm:grid-cols-2 lg:grid-cols-2">
      <!-- Bedrijf Blok -->
      <div>
        <div
          class="flex  items-center justify-center w-16 h-16 mx-auto text-gray-500 bg-gray-100 rounded-full dark:bg-gray-800 dark:text-white">
          <svg aria-hidden="true" class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
            fill="currentColor">
            <path fill-rule="evenodd"
              d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z"
              clip-rule="evenodd" />
          </svg>
        </div>
        <div class="mt-4">
          <figure class="flex items-center my-4">
            <img class="h-8 sm:h-12 w-auto mx-auto" src="<?php echo get_field('bedrijfslogo')['url']; ?>" alt="">
          </figure>
          <h3 class="text-xl font-bold text-gray-900 dark:text-white">
            <?php echo ($field_bedrijfnaam); ?>
          </h3>
          <div class="mt-1 text-base font-normal text-gray-500 dark:text-gray-400">
            <?php echo ($field_straat); ?>
            <?php echo ($field_postcode); ?>
          </div>
        </div>
      </div>

      <!-- Contact Blok -->
      <div>
        <div
          class="flex items-center justify-center w-16 h-16 mx-auto text-gray-500 bg-gray-100 rounded-full dark:bg-gray-800 dark:text-white">
          <svg aria-hidden="true" class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
            fill="currentColor">
            <path
              d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
          </svg>
        </div>
        <div class="mt-4">
          <h3 class="text-xl font-bold text-gray-900 dark:text-white">
            Contact
          </h3>
          <?php if ($field_telefoonnummer): ?>
          <div class="mt-1 text-base font-normal text-gray-500 dark:text-gray-400">
            Telefoon: <?php echo ($field_telefoonnummer); ?>
          </div>
          <?php endif; ?>
          <?php if ($field_email): ?>
          <a href="mailto:<?php echo esc_attr($field_email); ?>"
            class="block mt-1 text-base font-normal text-gray-500 dark:text-white hover:underline">
            E-mail: <?php echo ($field_email); ?>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="max-w-3xl mx-auto mt-8 lg:mt-24">
    <?php echo do_shortcode(get_field('form')) ?>
  </div>
</section>