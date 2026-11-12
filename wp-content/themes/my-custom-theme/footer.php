<?php
$email      = mahaba_text('mahaba_email');
$reg_number = mahaba_text('mahaba_reg_number');
$copy       = str_replace('{year}', wp_date('Y'), mahaba_text('mahaba_copyright'));
$facebook   = get_theme_mod('mahaba_facebook');
$instagram  = get_theme_mod('mahaba_instagram');
?>
<footer class="site-footer">
    <div class="footer-inner">
        <p class="footer-item">
            <?php if ($email) : ?>
                <?php echo esc_html(mahaba_text('mahaba_email_label')); ?>
                <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
            <?php endif; ?>
        </p>

        <div class="footer-contact-group">
            <?php if ($reg_number) : ?>
                <p class="footer-item">
                    <?php echo esc_html(mahaba_text('mahaba_reg_label')); ?>
                    <?php echo esc_html($reg_number); ?>
                </p>
            <?php endif; ?>

            <?php if ($facebook || $instagram) : ?>
                <div class="footer-socials">
                    <?php if ($facebook) : ?>
                        <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M13.5 21v-8h2.7l.4-3.2h-3.1V7.8c0-.9.3-1.5 1.6-1.5h1.7V3.4c-.3 0-1.3-.1-2.4-.1-2.4 0-4.1 1.5-4.1 4.2v2.3H7.5V13h2.8v8h3.2z" />
                            </svg>
                        </a>
                    <?php endif; ?>
                    <?php if ($instagram) : ?>
                        <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="3" width="18" height="18" rx="5" />
                                <circle cx="12" cy="12" r="4" />
                                <circle class="footer-social-dot" cx="17.2" cy="6.8" r="1" />
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <p class="footer-item"><?php echo esc_html($copy); ?></p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>

</html>