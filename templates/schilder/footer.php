<footer class="p-4 bg-white md:p-8 lg:p-10 dark:bg-gray-800">
    <div class="mx-auto max-w-screen-xl text-center">


        <ul class="flex flex-wrap justify-center items-center mb-6 text-gray-900 dark:text-white">
            <li>
                <a href="#hero" class="mr-4 hover:underline md:mr-6 ">Home</a>
            </li>
            <li>
                <a href="#features" class="mr-4 hover:underline md:mr-6">Diensten</a>
            </li>
            <li>
                <a href="#projecten" class="mr-4 hover:underline md:mr-6 ">Projecten</a>
            </li>
            <li>
                <a href="#overmij" class="mr-4 hover:underline md:mr-6">Over mij</a>
            </li>
            <li>
                <a href="#contact" class="mr-4 hover:underline md:mr-6">Contact</a>
            </li>

        </ul>
        <span class="text-sm text-gray-500 flex items-center justify-center gap-1 dark:text-gray-400">
            © 2025&nbsp;
            <a href="<?php echo get_field('website_url'); ?>" class="hover:underline"><?php echo get_field('bedrijfnaam'); ?></a>&nbsp;
            &nbsp;Website door&nbsp;
            <a class="underline" href="https://geregeld.online" target="_blank">Geregeld.online</a>
        </span>
    </div>
</footer>
<?php get_footer(); ?>