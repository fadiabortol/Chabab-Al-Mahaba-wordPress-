<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="<?php echo esc_url(function_exists('pll_home_url') ? pll_home_url() : home_url('/')); ?>">
                <?php echo mahaba_img('mahaba_logo', 'medium', get_bloginfo('name')); ?>
                <span class="brand-copy">
                    <span class="brand-name"><?php bloginfo('name'); ?></span>
                    <span class="brand-slogan"><?php bloginfo('description'); ?></span>
                </span>
            </a>

            <?php
            wp_nav_menu([
                'theme_location'  => 'primary',
                'container'       => 'nav',
                'container_class' => 'primary-nav',
                'menu_class'      => 'menu-list',
                'fallback_cb'     => false,
            ]);
            ?>

            <div class="header-lang">
                <?php
                if (shortcode_exists('google-translator')) {
                    echo do_shortcode('[google-translator]');
                } elseif (function_exists('pll_the_languages')) {
                    $mahaba_langs = pll_the_languages(['raw' => 1]);
                    if (!empty($mahaba_langs) && is_array($mahaba_langs)) : ?>
                        <ul class="language-switch">
                            <?php foreach ($mahaba_langs as $lang) : ?>
                                <li class="lang-item<?php echo !empty($lang['current_lang']) ? ' current-lang' : ''; ?>">
                                    <a href="<?php echo esc_url($lang['url']); ?>"
                                        lang="<?php echo esc_attr($lang['locale']); ?>"
                                        hreflang="<?php echo esc_attr($lang['locale']); ?>"
                                        <?php echo !empty($lang['current_lang']) ? 'aria-current="page"' : ''; ?>>
                                        <?php echo esc_html($lang['slug']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                <?php endif;
                }
                ?>
            </div>
        </div>
    </header>