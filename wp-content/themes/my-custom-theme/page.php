<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <?php if (has_shortcode(get_the_content(), 'mahaba_contact')) : ?>

            <main class="page-content contact-page">
                <h1 class="contact-title"><?php the_title(); ?></h1>
                <div class="contact-body">
                    <?php the_content(); ?>
                </div>
            </main>

        <?php else : ?>

            <main class="page-content">
                <article class="content-card">
                    <h1><?php the_title(); ?></h1>
                    <?php the_content(); ?>
                </article>
            </main>

        <?php endif; ?>

<?php endwhile;
endif; ?>

<?php get_footer(); ?>