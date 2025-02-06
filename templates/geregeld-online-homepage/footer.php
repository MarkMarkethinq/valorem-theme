<footer class="bg-white rounded-lg m-4">
    <div class="w-full max-w-screen-xl mx-auto p-4 md:py-8">
        <div class="sm:flex sm:items-center sm:justify-between">
            <a href="/" class="flex justify-center sm:justify-start items-center mb-4 sm:mb-0 space-x-3 rtl:space-x-reverse">
                <img src="<?php echo get_template_directory_uri(); ?>/templates/geregeld-online-homepage/img/logo.png" class="h-8" alt="Flowbite Logo" />
            </a>
            <ul class="flex flex-wrap justify-center sm:justify-start items-center mb-6 text-sm font-medium text-gray-500 sm:mb-0">
                <li>
                    <a href="/" class="hover:underline me-4 md:me-6">Home</a>
                </li>
                <li>
                    <a href="#wat-is-het" class="hover:underline me-4 md:me-6">Wat is het</a>
                </li>
                <li>
                    <a href="#hoe-het-werkt" class="hover:underline me-4 md:me-6">Hoe het werkt</a>
                </li>
                <li>
                    <a href="#kosten" class="hover:underline me-4 md:me-6">Kosten</a>
                </li>
                <li>
                    <a href="/aanmelden" class="hover:underline me-4 md:me-6">Aanmelden</a>
                </li>
                <li>
                    <a href="#veelgestelde-vragen" class="hover:underline me-4 md:me-6">Veelgestelde vragen</a>
                </li>
                <li>
                    <a href="#contact" class="hover:underline">Contact</a>
                </li>
            </ul>
        </div>
        <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
        <span class="block text-sm text-gray-500 text-center sm:text-left dark:text-gray-400">© <?php echo date('Y'); ?> Geregeld Online.</span>
    </div>
</footer>
<?php get_footer(); ?>
