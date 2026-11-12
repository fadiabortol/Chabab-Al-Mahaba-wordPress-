<?php
/*
Template Name: Login Page
*/

// Process login before loading header
$login_error = '';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['login_submit'])
) {

    if (
        !isset($_POST['login_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['login_nonce'])),
            'custom_login_action'
        )
    ) {

        $login_error = 'طلب غير صالح، يرجى المحاولة مجدداً.';
    } else {

        $credentials = array(
            'user_login' => sanitize_text_field(
                wp_unslash($_POST['username'] ?? '')
            ),

            'user_password' => $_POST['password'] ?? '',

            'remember' => isset($_POST['remember']),
        );

        $user = wp_signon($credentials, is_ssl());

        if (is_wp_error($user)) {

            $login_error = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
        } else {

            wp_safe_redirect(home_url('/'));
            exit;
        }
    }
}

get_header();


// ACF Content
$welcome_title = get_field('welcome_title') ?: 'أهلاً بكم!';

$welcome_description = get_field('welcome_description')
    ?: 'سجّل الدخول إلى حسابك للانضمام إلى مجتمعنا.';

$login_button_text = get_field('login_button_text')
    ?: 'تسجيل الدخول';

$footer_text = get_field('login_footer_text')
    ?: 'شباب المحبة - دير عمار';


// Get Register Page
$register_page = get_page_by_path('register');

$register_url = $register_page
    ? get_permalink($register_page->ID)
    : home_url('/register/');

?>

<section class="login-section">

    <div class="login-box">

        <div class="login-heading">

            <h2>
                <?php echo esc_html($welcome_title); ?>
            </h2>

            <p>
                <?php echo esc_html($welcome_description); ?>
            </p>

        </div>


        <?php if (isset($_GET['registered']) && $_GET['registered'] === '1') : ?>

            <div class="login-success">
                تم إنشاء حسابك بنجاح! يمكنك تسجيل الدخول الآن.
            </div>

        <?php endif; ?>


        <?php if ($login_error) : ?>

            <div class="login-error">
                <?php echo esc_html($login_error); ?>
            </div>

        <?php endif; ?>


        <form method="POST" class="custom-login-form">

            <?php
            wp_nonce_field(
                'custom_login_action',
                'login_nonce'
            );
            ?>

            <div class="login-input">

                <input
                    type="text"
                    name="username"
                    placeholder="اسم المستخدم أو البريد الإلكتروني"
                    autocomplete="username"
                    required>

            </div>


            <div class="login-input">

                <input
                    type="password"
                    name="password"
                    id="login-password"
                    placeholder="كلمة المرور"
                    autocomplete="current-password"
                    required>

            </div>


            <button
                type="submit"
                name="login_submit"
                class="login-button">

                <?php echo esc_html($login_button_text); ?>

            </button>

        </form>


        <!-- Create New Account -->
        <div class="create-account">

            <p>ما عندك حساب بعد؟</p>

            <a href="<?php echo esc_url($register_url); ?>">
                إنشاء حساب جديد
            </a>

        </div>


        <div class="login-footer">

            <p>
                <?php echo esc_html($footer_text); ?>
            </p>

        </div>

    </div>

</section>

<?php get_footer(); ?>