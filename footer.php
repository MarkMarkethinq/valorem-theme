<?php
/**
 * The template for displaying the footer
 */
?>

<footer class="bg-primary-900">
    <div class="mx-auto w-full max-w-screen-xl p-4 py-6 lg:py-8">
         
        <hr class="my-6 border-white sm:mx-auto lg:my-8" />
        <div class="flex flex-row items-center justify-between gap-2">
            <span class="text-[11px] sm:text-sm text-white shrink">
                <p>© <?php echo date('Y'); ?> Valorem Consultancy. Alle rechten voorbehouden. Ontwikkeld door <a href="https://developing.nl" target="_blank">Developing.</a></p>
            </span>
            <div class="flex justify-center shrink-0">
                <a href="https://www.linkedin.com/in/bart-van-der-wolf-573a26b3?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" target="_blank" class="text-white">
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