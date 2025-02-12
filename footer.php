<?php
/**
 * The template for displaying the footer
 */
?>

</main>

<footer class="bg-white dark:bg-gray-900">
    <div class="mx-auto w-full max-w-screen-xl p-4 py-6 lg:py-8">
         
        <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
        <div class="flex flex-row items-center justify-between gap-2">
            <span class="text-[11px] sm:text-sm text-gray-500 dark:text-gray-400 shrink">
                © <?php echo date('Y'); ?> <a href="<?php echo home_url(); ?>" class="hover:underline">Valorem Consultancy. Alle rechten voorbehouden.</a>
            </span>
            <div class="flex justify-center shrink-0">
                <a href="#" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                    </svg>
                    <span class="sr-only">LinkedIn</span>
                </a>
            </div>
        </div>
    </div>
    <script src="<?php echo get_template_directory_uri(); ?>/node_modules/flowbite/dist/flowbite.min.js"></script>
</footer>

<?php wp_footer(); ?>
</body>
</html>