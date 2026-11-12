<?php get_header(); ?>

<?php
$vid = mahaba_youtube_id(mahaba_text('mahaba_video_id'));

$args = [
    'post_type'      => 'mahaba_story',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => ['menu_order' => 'ASC', 'date' => 'DESC'],
];
if (function_exists('pll_current_language')) {
    $args['lang'] = pll_current_language();
}
$stories = new WP_Query($args);
?>

<main class="page-content">
    <section class="video-section">
        <?php if ($vid) : ?>
            <div class="video-frame">
                <iframe
                    src="https://www.youtube.com/embed/<?php echo esc_attr($vid); ?>?rel=0"
                    title="<?php echo esc_attr(mahaba_text('mahaba_journey_title')); ?>"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen
                    loading="lazy"></iframe>
            </div>
        <?php endif; ?>
        <h1><?php echo esc_html(mahaba_text('mahaba_journey_title')); ?></h1>
    </section>

    <?php while ($stories->have_posts()) : $stories->the_post();
        $gallery = array_filter(array_map('intval', explode(',', (string) get_post_meta(get_the_ID(), '_mahaba_gallery', true))));
    ?>
        <section class="story-section<?php echo $gallery ? '' : ' no-gallery'; ?>">
            <div class="story-copy">
                <h2><?php the_title(); ?></h2>
                <?php the_content(); ?>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="story-link-wrapper">
                        <div class="story-link-image"><?php the_post_thumbnail('large'); ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($gallery) : ?>
                <div class="image-gallery">
                    <?php foreach ($gallery as $img_id) {
                        echo wp_get_attachment_image($img_id, 'large');
                    } ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endwhile;
    wp_reset_postdata(); ?>
</main>

<?php get_footer(); ?>