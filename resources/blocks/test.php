<h1 class="text-4xl font-bold text-center text-red-500"><?php echo get_field('title'); ?></h1>

<?php if (get_field('link')): ?>
    <a href="<?php echo get_field('link')['url']; ?>" target="<?php echo get_field('link')['target']; ?>" class="btn btn-primary"><?php echo get_field('link')['title']; ?></a>
<?php endif; ?>

<?php if (get_field('image')): ?>
    <img src="<?php echo get_field('image')['url']; ?>" alt="<?php echo get_field('image')['alt']; ?>" class="w-full h-auto">
<?php endif; ?>


