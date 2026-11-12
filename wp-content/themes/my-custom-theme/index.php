<?php get_header(); ?>
<main class="page-content">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article class="content-card">
                <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                <?php the_excerpt(); ?>
            </article>
        <?php endwhile; ?>

        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <article class="content-card">
            <h1><?php esc_html_e('Nothing found', 'my-custom-theme'); ?></h1>
            <p><?php esc_html_e('No content was found here.', 'my-custom-theme'); ?></p>
        </article>
    <?php endif; ?>
</main>
<?php get_footer(); ?>