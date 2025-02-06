<?php
add_action('wp_footer', 'first_time_popup');
function first_time_popup() {
?>

<div id="first-time-popup" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-[9999] w-full md:inset-0 h-modal md:h-full">
  <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
      <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 md:p-8">
          <button type="button" class="text-gray-400 absolute top-2 right-2 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" id="close-popup">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>  
          </button>
          <div class="mb-4 text-sm font-light text-gray-500 dark:text-gray-400">
              <h3 class="mb-3 text-2xl font-bold text-gray-900 dark:text-white">Welkom op je nieuwe website!</h3>
              <p>
              Dit is het eerste voorbeeld van hoe jouw website eruitziet.
              <br><br>
              Heb je feedback of suggesties? Klik op de "Feedback" knop rechtsonder op je scherm.
              <br><br>
              Liever direct contact? Stuur ons een bericht via WhatsApp.
              </p>
          </div>
      </div>
  </div>
</div>

<?php
}