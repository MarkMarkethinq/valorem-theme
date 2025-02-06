<?php
get_header(); // Laadt de header.php
?>

<main id="main-content">
    <div class="container">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                        <div class="entry-meta">
                            <span class="posted-on">Geplaatst op: <?php echo get_the_date(); ?></span>
                            <span class="author">Door: <?php the_author(); ?></span>
                        </div>
                    </header>

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>

                    <footer class="entry-footer">
                        <?php if (has_category()) : ?>
                            <div class="categories">
                                Categorieën: <?php the_category(', '); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (has_tag()) : ?>
                            <div class="tags">
                                Tags: <?php the_tags('', ', ', ''); ?>
                            </div>
                        <?php endif; ?>
                    </footer>
                </article>

                <?php
                // Reacties sectie
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>

        <?php endwhile;
        else :
            echo '<p>Geen berichten gevonden.</p>';
        endif;
        ?>
    </div>
</main>

<?php
get_sidebar(); // Laadt de sidebar.php (optioneel)
get_footer(); // Laadt de footer.php
?>