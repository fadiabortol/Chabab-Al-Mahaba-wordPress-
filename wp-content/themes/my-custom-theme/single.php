<?php get_header(); ?>
<main class="page-content">
    <?php while (have_posts()) : the_post(); ?>
        <article class="content-card">
            <h1><?php the_title(); ?></h1>
            <time class="post-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                <?php echo esc_html(get_the_date()); ?>
            </time>

            <?php if (has_post_thumbnail()) : ?>
                <div class="post-thumb"><?php the_post_thumbnail('large'); ?></div>
            <?php endif; ?>

            <?php the_content(); ?>
            <?php wp_link_pages(); ?>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>