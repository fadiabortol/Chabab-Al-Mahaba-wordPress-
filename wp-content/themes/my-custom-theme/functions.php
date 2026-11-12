<?php
/* =====================================================================
   1. THEME SETUP
   ===================================================================== */
function mahaba_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    register_nav_menus([
        'primary' => 'Main Menu',
    ]);
}
add_action('after_setup_theme', 'mahaba_setup');

function mahaba_asset_version($relative_path)
{
    $file = get_template_directory() . $relative_path;
    return file_exists($file) ? filemtime($file) : null;
}

function mahaba_assets()
{
    wp_enqueue_style(
        'mahaba-main',
        get_template_directory_uri() . '/assets/css/main.css',
        [],
        mahaba_asset_version('/assets/css/main.css')
    );

    if (file_exists(get_template_directory() . '/assets/js/main.js')) {
        wp_enqueue_script(
            'mahaba-js',
            get_template_directory_uri() . '/assets/js/main.js',
            [],
            mahaba_asset_version('/assets/js/main.js'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'mahaba_assets');


/* =====================================================================
   2. HELPERS
   ===================================================================== */
function mahaba_text($key, $default = '')
{
    $translatable = mahaba_translatable();
    if (isset($translatable[$key])) {
        $default = $translatable[$key][0];
    }
    $value = get_theme_mod($key, $default);
    if ($value && isset($translatable[$key]) && function_exists('pll__')) {
        return pll__($value);
    }
    return $value;
}

function mahaba_img($key, $size = 'large', $alt = '')
{
    $id = get_theme_mod($key);
    if (!$id) return '';
    return wp_get_attachment_image($id, $size, false, ['alt' => $alt]);
}

function mahaba_len($string)
{
    return function_exists('mb_strlen') ? mb_strlen($string) : strlen($string);
}

/* Byeqbal el ID aw el rabet kemel w byerja3 el YouTube ID */
function mahaba_youtube_id($input)
{
    $input = trim((string) $input);
    if ($input === '') return '';
    if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/|live/))([A-Za-z0-9_-]{11})~', $input, $m)) {
        return $m[1];
    }
    return preg_match('~^[A-Za-z0-9_-]{11}$~', $input) ? $input : '';
}


/* =====================================================================
   3. STORIES (Custom Post Type) - "7kayetna"
   ===================================================================== */
function mahaba_register_story_cpt()
{
    register_post_type('mahaba_story', [
        'labels' => [
            'name'          => 'Stories',
            'singular_name' => 'Story',
            'menu_name'     => 'Stories',
            'all_items'     => 'All stories',
            'add_new'       => 'Add new',
            'add_new_item'  => 'Add new story',
            'edit_item'     => 'Edit story',
        ],
        'public'        => false,
        'show_ui'       => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-book-alt',
        'menu_position' => 20,
        'supports'      => ['title', 'editor', 'thumbnail', 'page-attributes'],
    ]);
}
add_action('init', 'mahaba_register_story_cpt');

// Polylang: fa3el el tarjame lal Stories automatic
add_filter('pll_get_post_types', function ($types) {
    $types['mahaba_story'] = 'mahaba_story';
    return $types;
});

// Meta box: el sowar el jenbiye
function mahaba_story_metabox()
{
    add_meta_box('mahaba_story_images', 'Side images', 'mahaba_story_images_box', 'mahaba_story', 'normal', 'high');
}
add_action('add_meta_boxes', 'mahaba_story_metabox');

add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) return;
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'mahaba_story') {
        wp_enqueue_media();
    }
});

function mahaba_story_images_box($post)
{
    wp_nonce_field('mahaba_story_images', 'mahaba_story_nonce');
    $ids = (string) get_post_meta($post->ID, '_mahaba_gallery', true);
?>
    <input type="hidden" id="mahaba-gallery-ids" name="mahaba_gallery" value="<?php echo esc_attr($ids); ?>">
    <div id="mahaba-gallery-preview" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px">
        <?php foreach (array_filter(explode(',', $ids)) as $id) {
            echo wp_get_attachment_image((int) $id, 'thumbnail', false, ['style' => 'width:100px;height:100px;object-fit:cover']);
        } ?>
    </div>
    <button type="button" class="button" id="mahaba-gallery-btn">Choose images</button>
    <button type="button" class="button" id="mahaba-gallery-clear">Remove all</button>
    <p class="description">Ekhtar 7atta 3 sowar mn el Media Library. Btetla3 jenb el nass (w el sowra el 3ariyda taht el nass hiye el Featured image).</p>
    <script>
        jQuery(function($) {
            var frame;
            $('#mahaba-gallery-btn').on('click', function(e) {
                e.preventDefault();
                if (!frame) {
                    frame = wp.media({
                        title: 'Choose images',
                        multiple: true,
                        library: {
                            type: 'image'
                        },
                        button: {
                            text: 'Use these images'
                        }
                    });
                    frame.on('select', function() {
                        var ids = [],
                            html = '';
                        frame.state().get('selection').each(function(a) {
                            var j = a.toJSON();
                            ids.push(j.id);
                            var src = (j.sizes && j.sizes.thumbnail) ? j.sizes.thumbnail.url : j.url;
                            html += '<img src="' + src + '" style="width:100px;height:100px;object-fit:cover">';
                        });
                        $('#mahaba-gallery-ids').val(ids.join(','));
                        $('#mahaba-gallery-preview').html(html);
                    });
                }
                frame.open();
            });
            $('#mahaba-gallery-clear').on('click', function() {
                $('#mahaba-gallery-ids').val('');
                $('#mahaba-gallery-preview').empty();
            });
        });
    </script>
<?php
}

add_action('save_post_mahaba_story', function ($post_id) {
    if (!isset($_POST['mahaba_story_nonce']) || !wp_verify_nonce($_POST['mahaba_story_nonce'], 'mahaba_story_images')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $ids = isset($_POST['mahaba_gallery']) ? preg_replace('/[^0-9,]/', '', wp_unslash($_POST['mahaba_gallery'])) : '';
    update_post_meta($post_id, '_mahaba_gallery', $ids);
});


/* =====================================================================
   4. CONTACT: el nosous [key => [label, default, type]]
   ===================================================================== */
function mahaba_contact_fields()
{
    return [
        'mahaba_c_first_name'    => ['First name label',       'First name',                 'text'],
        'mahaba_c_first_name_ph' => ['First name placeholder', 'Enter your first name',      'text'],
        'mahaba_c_last_name'     => ['Last name label',        'Last name',                  'text'],
        'mahaba_c_last_name_ph'  => ['Last name placeholder',  'Enter your last name',       'text'],
        'mahaba_c_email'         => ['Email label',            'Your email',                 'text'],
        'mahaba_c_email_ph'      => ['Email placeholder',      'you@example.com',            'text'],
        'mahaba_c_phone'         => ['Phone label',            'Phone number',               'text'],
        'mahaba_c_phone_ph'      => ['Phone placeholder',      '00 000 000',                 'text'],
        'mahaba_c_country_code'  => ['Country code label',     'Country code',               'text'],
        'mahaba_c_city'          => ['City label',             'City',                       'text'],
        'mahaba_c_city_ph'       => ['City placeholder',       'Enter your city',            'text'],
        'mahaba_c_message'       => ['Message label',          'Message',                    'text'],
        'mahaba_c_message_ph'    => ['Message placeholder',    'Leave us a message...',      'text'],
        'mahaba_c_send'          => ['Send button',            'Send message',               'text'],
        'mahaba_c_success'       => ['Success message',        'Thank you, your message has been sent.', 'text'],
        'mahaba_c_invalid'       => ['Invalid form message',   'Please check the fields and try again.', 'text'],
        'mahaba_c_error'         => ['Send error message',     'Your message could not be sent. Please try again later.', 'text'],
    ];
}

function mahaba_phone_countries()
{
    return [
        'LB' => ['🇱🇧 Lebanon', '+961'],
        'US' => ['🇺🇸 United States', '+1'],
        'CA' => ['🇨🇦 Canada', '+1'],
        'GB' => ['🇬🇧 United Kingdom', '+44'],
        'FR' => ['🇫🇷 France', '+33'],
        'TR' => ['🇹🇷 Türkiye', '+90'],
        'SY' => ['🇸🇾 Syria', '+963'],
        'JO' => ['🇯🇴 Jordan', '+962'],
        'PS' => ['🇵🇸 Palestine', '+970'],
        'AE' => ['🇦🇪 United Arab Emirates', '+971'],
        'SA' => ['🇸🇦 Saudi Arabia', '+966'],
        'QA' => ['🇶🇦 Qatar', '+974'],
        'KW' => ['🇰🇼 Kuwait', '+965'],
        'BH' => ['🇧🇭 Bahrain', '+973'],
        'OM' => ['🇴🇲 Oman', '+968'],
        'EG' => ['🇪🇬 Egypt', '+20'],
        'IQ' => ['🇮🇶 Iraq', '+964'],
        'MA' => ['🇲🇦 Morocco', '+212'],
        'TN' => ['🇹🇳 Tunisia', '+216'],
        'DZ' => ['🇩🇿 Algeria', '+213'],
        'DE' => ['🇩🇪 Germany', '+49'],
        'IT' => ['🇮🇹 Italy', '+39'],
        'ES' => ['🇪🇸 Spain', '+34'],
        'NL' => ['🇳🇱 Netherlands', '+31'],
        'BE' => ['🇧🇪 Belgium', '+32'],
        'CH' => ['🇨🇭 Switzerland', '+41'],
        'SE' => ['🇸🇪 Sweden', '+46'],
        'NO' => ['🇳🇴 Norway', '+47'],
        'DK' => ['🇩🇰 Denmark', '+45'],
        'GR' => ['🇬🇷 Greece', '+30'],
        'CY' => ['🇨🇾 Cyprus', '+357'],
        'IE' => ['🇮🇪 Ireland', '+353'],
        'AU' => ['🇦🇺 Australia', '+61'],
        'NZ' => ['🇳🇿 New Zealand', '+64'],
        'BR' => ['🇧🇷 Brazil', '+55'],
        'MX' => ['🇲🇽 Mexico', '+52'],
        'IN' => ['🇮🇳 India', '+91'],
        'PK' => ['🇵🇰 Pakistan', '+92'],
        'BD' => ['🇧🇩 Bangladesh', '+880'],
        'JP' => ['🇯🇵 Japan', '+81'],
        'CN' => ['🇨🇳 China', '+86'],
        'KR' => ['🇰🇷 South Korea', '+82'],
        'NG' => ['🇳🇬 Nigeria', '+234'],
        'ZA' => ['🇿🇦 South Africa', '+27'],
        'RU' => ['🇷🇺 Russia', '+7'],
    ];
}


/* =====================================================================
   5. CUSTOMIZER + POLYLANG STRINGS
   ===================================================================== */

/* El nosous elli btetterjam (key => [default, multiline]) */
function mahaba_translatable()
{
    $list = [
        'mahaba_journey_title' => ['Our Journey in 15 Minutes', false],
        'mahaba_email_label'   => ['Email:', false],
        'mahaba_reg_label'     => ['Registration No.:', false],
        'mahaba_copyright'     => ['© {year} All rights reserved.', false],
    ];
    foreach (mahaba_contact_fields() as $key => $f) {
        $list[$key] = [$f[1], false];
    }
    return $list;
}

function mahaba_customizer($wp_customize)
{
    $sections = [
        'mahaba_header'  => 'Header',
        'mahaba_home'    => 'Home Page',
        'mahaba_contact' => 'Contact Page',
        'mahaba_footer'  => 'Footer',
    ];
    $p = 30;
    foreach ($sections as $id => $title) {
        $wp_customize->add_section($id, ['title' => $title, 'priority' => $p++]);
    }

    // [key, label, section, type, default, sanitize]
    $fields = [
        ['mahaba_video_id',      'YouTube Video ID',  'mahaba_home', 'text', '', 'sanitize_text_field'],
        ['mahaba_journey_title', 'Title under video', 'mahaba_home', 'text', 'Our Journey in 15 Minutes', 'sanitize_text_field'],

        ['mahaba_contact_recipient', 'Email elli btousal 3alayh el rasa2el', 'mahaba_contact', 'text', get_option('admin_email'), 'sanitize_email'],

        ['mahaba_email',       'Email',                       'mahaba_footer', 'text', '', 'sanitize_text_field'],
        ['mahaba_email_label', 'Email label',                 'mahaba_footer', 'text', 'Email:', 'sanitize_text_field'],
        ['mahaba_reg_label',   'Registration label',          'mahaba_footer', 'text', 'Registration No.:', 'sanitize_text_field'],
        ['mahaba_reg_number',  'Registration number',         'mahaba_footer', 'text', '', 'sanitize_text_field'],
        ['mahaba_facebook',    'Facebook URL',                'mahaba_footer', 'url',  '', 'esc_url_raw'],
        ['mahaba_instagram',   'Instagram URL',               'mahaba_footer', 'url',  '', 'esc_url_raw'],
        ['mahaba_copyright',   'Copyright text (use {year})', 'mahaba_footer', 'text', '© {year} All rights reserved.', 'sanitize_text_field'],
    ];

    foreach (mahaba_contact_fields() as $key => $f) {
        $fields[] = [$key, $f[0], 'mahaba_contact', $f[2], $f[1], 'sanitize_text_field'];
    }

    foreach ($fields as $f) {
        $wp_customize->add_setting($f[0], [
            'default'           => $f[4],
            'sanitize_callback' => $f[5],
        ]);
        $wp_customize->add_control($f[0], [
            'label'   => $f[1],
            'section' => $f[2],
            'type'    => $f[3],
        ]);
    }

    // Logo
    $wp_customize->add_setting('mahaba_logo', ['default' => 0, 'sanitize_callback' => 'absint']);
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'mahaba_logo', [
        'label'     => 'Logo',
        'section'   => 'mahaba_header',
        'mime_type' => 'image',
    ]));
}
add_action('customize_register', 'mahaba_customizer');

/* Polylang: sajjel el nosous la tetterjam */
function mahaba_register_strings()
{
    if (!function_exists('pll_register_string')) return;
    foreach (mahaba_translatable() as $key => $info) {
        $value = get_theme_mod($key, $info[0]);
        if ($value) {
            pll_register_string($key, $value, 'Mahaba Theme', $info[1]);
        }
    }
}
add_action('init', 'mahaba_register_strings');


/* =====================================================================
   6. CONTACT FORM: el ersal + el shortcode [mahaba_contact]
   ===================================================================== */
function mahaba_handle_contact()
{
    $back = wp_get_referer() ?: home_url('/');
    $back = remove_query_arg('contact', $back);

    if (!isset($_POST['mahaba_nonce']) || !wp_verify_nonce($_POST['mahaba_nonce'], 'mahaba_contact')) {
        wp_safe_redirect(add_query_arg('contact', 'invalid', $back));
        exit;
    }

    // Honeypot: eza el bot 3abba, nhoss ka2anno najah
    if (!empty($_POST['website'])) {
        wp_safe_redirect(add_query_arg('contact', 'success', $back));
        exit;
    }

    $countries = mahaba_phone_countries();
    $first   = sanitize_text_field(wp_unslash($_POST['first_name'] ?? ''));
    $last    = sanitize_text_field(wp_unslash($_POST['last_name'] ?? ''));
    $email   = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $phone   = sanitize_text_field(wp_unslash($_POST['phone'] ?? ''));
    $city    = sanitize_text_field(wp_unslash($_POST['city'] ?? ''));
    $country = sanitize_text_field(wp_unslash($_POST['phone_country'] ?? ''));
    $message = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));

    $digits = preg_replace('/\D/', '', $phone);
    $phone_ok = isset($countries[$country]) && (
        $phone === '' || (
            preg_match('/\A[0-9\s().-]+\z/', $phone)
            && strlen($digits) >= 4
            && strlen($digits) + strlen(ltrim($countries[$country][1], '+')) <= 15
        )
    );

    if (
        mahaba_len($first) < 1 || mahaba_len($first) > 100 ||
        mahaba_len($last) < 1 || mahaba_len($last) > 100 ||
        !is_email($email) || strlen($email) > 254 ||
        !$phone_ok ||
        mahaba_len($city) > 190 ||
        mahaba_len($message) < 1 || mahaba_len($message) > 5000
    ) {
        wp_safe_redirect(add_query_arg('contact', 'invalid', $back));
        exit;
    }

    $to = get_theme_mod('mahaba_contact_recipient', get_option('admin_email'));
    if (!is_email($to)) $to = get_option('admin_email');

    $body = "Name: {$first} {$last}\nEmail: {$email}\n";
    if ($phone !== '') $body .= 'Phone: ' . $countries[$country][1] . " {$phone}\n";
    if ($city !== '')  $body .= "City: {$city}\n";
    $body .= "\nMessage:\n{$message}\n";

    $sent = wp_mail(
        $to,
        'New contact message - ' . get_bloginfo('name'),
        $body,
        ['Reply-To: ' . $email, 'Content-Type: text/plain; charset=UTF-8']
    );

    wp_safe_redirect(add_query_arg('contact', $sent ? 'success' : 'error', $back));
    exit;
}
add_action('admin_post_nopriv_mahaba_contact', 'mahaba_handle_contact');
add_action('admin_post_mahaba_contact', 'mahaba_handle_contact');

function mahaba_contact_shortcode()
{
    $status   = isset($_GET['contact']) ? sanitize_key($_GET['contact']) : '';
    $messages = [
        'success' => ['mahaba_c_success', true],
        'invalid' => ['mahaba_c_invalid', false],
        'error'   => ['mahaba_c_error',   false],
    ];
    $countries = mahaba_phone_countries();

    ob_start();
    if (isset($messages[$status])) {
        $m = $messages[$status];
        echo '<p class="form-message ' . ($m[1] ? 'success' : '') . '" role="status">' . esc_html(mahaba_text($m[0])) . '</p>';
    }
?>
    <form class="contact-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="mahaba_contact">
        <?php wp_nonce_field('mahaba_contact', 'mahaba_nonce'); ?>

        <label class="form-field">
            <?php echo esc_html(mahaba_text('mahaba_c_first_name')); ?>
            <input type="text" name="first_name" maxlength="100" autocomplete="given-name"
                placeholder="<?php echo esc_attr(mahaba_text('mahaba_c_first_name_ph')); ?>" required>
        </label>

        <label class="form-field">
            <?php echo esc_html(mahaba_text('mahaba_c_last_name')); ?>
            <input type="text" name="last_name" maxlength="100" autocomplete="family-name"
                placeholder="<?php echo esc_attr(mahaba_text('mahaba_c_last_name_ph')); ?>" required>
        </label>

        <label class="form-field">
            <?php echo esc_html(mahaba_text('mahaba_c_email')); ?>
            <input type="email" name="email" maxlength="254" autocomplete="email"
                placeholder="<?php echo esc_attr(mahaba_text('mahaba_c_email_ph')); ?>" required>
        </label>

        <div class="form-field">
            <label for="contact-phone"><?php echo esc_html(mahaba_text('mahaba_c_phone')); ?></label>
            <span class="phone-input-row" dir="ltr">
                <label class="visually-hidden" for="phone-country"><?php echo esc_html(mahaba_text('mahaba_c_country_code')); ?></label>
                <select id="phone-country" name="phone_country" required>
                    <?php foreach ($countries as $code => $c) : ?>
                        <option value="<?php echo esc_attr($code); ?>" <?php selected($code, 'LB'); ?>>
                            <?php echo esc_html($c[0] . ' (' . $c[1] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input id="contact-phone" type="tel" name="phone" maxlength="25" inputmode="tel" autocomplete="tel-national"
                    placeholder="<?php echo esc_attr(mahaba_text('mahaba_c_phone_ph')); ?>">
            </span>
        </div>

        <label class="form-field">
            <?php echo esc_html(mahaba_text('mahaba_c_city')); ?>
            <input type="text" name="city" maxlength="190" autocomplete="address-level2"
                placeholder="<?php echo esc_attr(mahaba_text('mahaba_c_city_ph')); ?>">
        </label>

        <label class="form-field">
            <?php echo esc_html(mahaba_text('mahaba_c_message')); ?>
            <textarea name="message" rows="6" maxlength="5000"
                placeholder="<?php echo esc_attr(mahaba_text('mahaba_c_message_ph')); ?>" required></textarea>
        </label>

        <div class="contact-honeypot" aria-hidden="true">
            <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
        </div>

        <button class="button-primary" type="submit"><?php echo esc_html(mahaba_text('mahaba_c_send')); ?></button>
    </form>
<?php
    return ob_get_clean();
}
add_shortcode('mahaba_contact', 'mahaba_contact_shortcode');


/* =====================================================================
   7. GOOGLE TRANSLATE: RTL/LTR TLQAYE
   ===================================================================== */
function mahaba_google_translate_dir()
{
?>
    <script>
        (function() {
            var html = document.documentElement;
            var origDir = html.getAttribute('dir') || 'ltr';
            var origLang = (html.getAttribute('lang') || 'en').split('-')[0].toLowerCase();
            var RTL_LANGS = ['ar', 'he', 'fa', 'ur'];
            var RTL_CHARS = /[\u0590-\u08FF]/g;
            var LETTERS = /[A-Za-z\u00C0-\u024F\u0590-\u08FF]/g;
            var SKIP = '.language-switch, .header-lang, select, .goog-te-gadget, .gtranslate_wrapper, .skiptranslate';
            var timer = null;
            var lastRtl = null;

            function sampleText() {
                var out = '';
                var roots = document.querySelectorAll(
                    'main, .page-content, .login-section, .register-section, .contact-page, ' +
                    '.site-header .primary-nav, .site-header .brand-name'
                );
                var seen = [];
                for (var i = 0; i < roots.length; i++) {
                    var el = roots[i];
                    if (el.closest(SKIP)) continue;
                    // ma nfa77es nafs el element marteyn
                    var dup = false;
                    for (var k = 0; k < seen.length; k++) {
                        if (seen[k].contains(el) || el.contains(seen[k])) { dup = true; break; }
                    }
                    if (dup) continue;
                    seen.push(el);
                    out += ' ' + (el.innerText || el.textContent || '');
                }
                var fields = document.querySelectorAll('input[placeholder], textarea[placeholder]');
                for (var j = 0; j < fields.length; j++) {
                    out += ' ' + fields[j].getAttribute('placeholder');
                }
                return out.slice(0, 8000);
            }

            function detectArabic() {
                var text = sampleText();
                var letters = text.match(LETTERS);
                if (!letters || letters.length < 10) return null;
                var rtl = text.match(RTL_CHARS);
                return ((rtl ? rtl.length : 0) / letters.length) > 0.3;
            }

            function fromCookie() {
                var m = document.cookie.match(/(?:^|;\s*)googtrans=([^;]*)/);
                if (!m) return null;
                var t = (decodeURIComponent(m[1]).split('/')[2] || '').toLowerCase().split('-')[0];
                if (!t) return null;
                if (t === origLang) return false;
                return RTL_LANGS.indexOf(t) > -1;
            }

            function apply() {
                var rtl;

                if (html.classList.contains('translated-rtl')) {
                    rtl = true;
                } else if (html.classList.contains('translated-ltr')) {
                    rtl = false;
                } else {
                    var detected = detectArabic();
                    if (detected !== null) {
                        rtl = detected;
                    } else {
                        var c = fromCookie();
                        rtl = (c !== null) ? c : (origDir === 'rtl');
                    }
                }

                var dir = rtl ? 'rtl' : 'ltr';
                if (html.getAttribute('dir') !== dir) html.setAttribute('dir', dir);
                if (rtl && !html.classList.contains('mahaba-rtl')) html.classList.add('mahaba-rtl');
                if (!rtl && html.classList.contains('mahaba-rtl')) html.classList.remove('mahaba-rtl');
                lastRtl = rtl;
            }

            function schedule() {
                clearTimeout(timer);
                timer = setTimeout(apply, 200);
            }

            function start() {
                apply();

                [500, 1500, 3000, 5000].forEach(function(ms) {
                    setTimeout(apply, ms);
                });

                new MutationObserver(schedule).observe(document.body, {
                    childList: true,
                    characterData: true,
                    subtree: true
                });

                new MutationObserver(schedule).observe(html, {
                    attributes: true,
                    attributeFilter: ['class']
                });

                document.addEventListener('change', function() {
                    setTimeout(apply, 300);
                    setTimeout(apply, 1200);
                    setTimeout(apply, 3000);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', start);
            } else {
                start();
            }
        })();
    </script>
<?php
}
add_action('wp_head', 'mahaba_google_translate_dir', 1);