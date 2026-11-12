<?php
/*
Template Name: Register Page
*/

get_header();

$errors = [];

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['register_user'])
) {

    // التحقق من الحماية
    if (
        !isset($_POST['register_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['register_nonce'])),
            'custom_register_action'
        )
    ) {
        $errors[] = 'حدث خطأ في التحقق الأمني. يرجى المحاولة مرة أخرى.';
    } else {

        // استقبال البيانات
        $first_name = sanitize_text_field(
            wp_unslash($_POST['first_name'] ?? '')
        );

        $last_name = sanitize_text_field(
            wp_unslash($_POST['last_name'] ?? '')
        );

        $birth_date = sanitize_text_field(
            wp_unslash($_POST['birth_date'] ?? '')
        );

        $contact = sanitize_text_field(
            wp_unslash($_POST['contact'] ?? '')
        );

        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        // التحقق من الحقول المطلوبة
        if (
            !$first_name ||
            !$last_name ||
            !$birth_date ||
            !$contact ||
            !$password ||
            !$confirm
        ) {
            $errors[] = 'يرجى تعبئة جميع الحقول.';
        }

        // التحقق من تاريخ الميلاد
        $date = DateTime::createFromFormat('!Y-m-d', $birth_date);

        if (
            !$date ||
            $date->format('Y-m-d') !== $birth_date ||
            $date > new DateTime('today')
        ) {
            $errors[] = 'يرجى إدخال تاريخ ميلاد صحيح.';
        }

        // التحقق من البريد الإلكتروني أو رقم الهاتف
        $is_email = is_email($contact);

        if (
            !$is_email &&
            !preg_match('/^\+?[0-9\s().-]{7,20}$/', $contact)
        ) {
            $errors[] = 'يرجى إدخال بريد إلكتروني أو رقم هاتف صحيح.';
        }

        // التحقق من كلمة المرور
        if (strlen($password) < 8) {
            $errors[] = 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل.';
        }

        // مطابقة كلمة المرور
        if ($password !== $confirm) {
            $errors[] = 'كلمتا المرور غير متطابقتين.';
        }

        // التحقق من عدم استخدام البريد أو الهاتف مسبقاً
        if (empty($errors)) {

            if ($is_email) {

                if (email_exists($contact)) {
                    $errors[] = 'هذا البريد الإلكتروني مستخدم مسبقاً.';
                }
            } else {

                $phone = preg_replace('/\D+/', '', $contact);

                $existing_phone = new WP_User_Query([
                    'number' => 1,
                    'fields' => 'ID',
                    'meta_query' => [
                        [
                            'key' => 'phone_number',
                            'value' => $phone,
                            'compare' => '='
                        ]
                    ]
                ]);

                if (!empty($existing_phone->get_results())) {
                    $errors[] = 'رقم الهاتف مستخدم مسبقاً.';
                }
            }
        }

        // إنشاء حساب WordPress
        if (empty($errors)) {

            if ($is_email) {

                $username = sanitize_user(
                    current(explode('@', $contact)),
                    true
                );

                if (!$username) {
                    $username = 'user';
                }

                $base_username = $username;
                $counter = 1;

                while (username_exists($username)) {
                    $username = $base_username . $counter;
                    $counter++;
                }

                $user_email = $contact;
            } else {

                $phone = preg_replace('/\D+/', '', $contact);

                $username = 'phone_' . $phone;
                $user_email = '';
            }

            $user_id = wp_insert_user([

                'user_login' => $username,

                'user_pass' => $password,

                'user_email' => $user_email,

                'first_name' => $first_name,

                'last_name' => $last_name,

                'display_name' => $first_name . ' ' . $last_name,

                'role' => 'subscriber'

            ]);

            if (is_wp_error($user_id)) {

                $errors[] = 'حدث خطأ أثناء إنشاء الحساب. يرجى المحاولة مرة أخرى.';
            } else {

                // حفظ تاريخ الميلاد
                update_user_meta(
                    $user_id,
                    'date_of_birth',
                    $birth_date
                );

                // حفظ رقم الهاتف إذا تم استخدامه
                if (!$is_email) {
                    update_user_meta(
                        $user_id,
                        'phone_number',
                        $phone
                    );
                }

                // الانتقال إلى صفحة تسجيل الدخول
                $login_page = get_page_by_path('login');

                $login_url = $login_page
                    ? get_permalink($login_page)
                    : home_url('/');

                wp_safe_redirect(
                    add_query_arg('registered', '1', $login_url)
                );

                exit;
            }
        }
    }
}
?>

<section class="register-section" dir="rtl">

    <div class="register-container">

        <div class="register-heading">

            <h2>إنشاء حساب جديد</h2>

            <p>أنشئ حسابك وابدأ باستخدام الموقع</p>

        </div>

        <?php if (!empty($errors)) : ?>

            <div class="register-errors">

                <?php foreach ($errors as $error) : ?>

                    <p>
                        <?php echo esc_html($error); ?>
                    </p>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>


        <form method="POST" class="register-form">

            <?php
            wp_nonce_field(
                'custom_register_action',
                'register_nonce'
            );
            ?>

            <div class="register-fields">

                <!-- الاسم الأول -->
                <div class="register-field">

                    <label for="first_name">الاسم الأول</label>

                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        placeholder="أدخل الاسم الأول"
                        value="<?php echo esc_attr($_POST['first_name'] ?? ''); ?>"
                        required>

                </div>


                <!-- اسم العائلة -->
                <div class="register-field">

                    <label for="last_name">اسم العائلة</label>

                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        placeholder="أدخل اسم العائلة"
                        value="<?php echo esc_attr($_POST['last_name'] ?? ''); ?>"
                        required>

                </div>


                <!-- تاريخ الميلاد -->
                <div class="register-field full-width">

                    <label for="birth_date">تاريخ الميلاد</label>

                    <input
                        type="date"
                        id="birth_date"
                        name="birth_date"
                        max="<?php echo esc_attr(wp_date('Y-m-d')); ?>"
                        value="<?php echo esc_attr($_POST['birth_date'] ?? ''); ?>"
                        required>

                </div>


                <!-- البريد الإلكتروني أو رقم الهاتف -->
                <div class="register-field full-width">

                    <label for="contact">البريد الإلكتروني أو رقم الهاتف</label>

                    <input
                        type="text"
                        id="contact"
                        name="contact"
                        placeholder="أدخل البريد الإلكتروني أو رقم الهاتف"
                        value="<?php echo esc_attr($_POST['contact'] ?? ''); ?>"
                        required>

                </div>


                <!-- كلمة المرور -->
                <div class="register-field full-width">

                    <label for="password">كلمة المرور</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="أدخل كلمة المرور"
                        minlength="8"
                        autocomplete="new-password"
                        required>

                </div>


                <!-- تأكيد كلمة المرور -->
                <div class="register-field full-width">

                    <label for="confirm_password">تأكيد كلمة المرور</label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="أعد إدخال كلمة المرور"
                        minlength="8"
                        autocomplete="new-password"
                        required>

                </div>

            </div>


            <!-- زر إنشاء الحساب -->
            <button
                type="submit"
                name="register_user"
                class="register-submit">
                إنشاء حساب جديد
            </button>

        </form>


        <!-- رابط تسجيل الدخول -->
        <div class="register-footer">

            <p>

                لديك حساب بالفعل؟

                <a href="<?php echo esc_url(home_url('/login/')); ?>">
                    تسجيل الدخول
                </a>

            </p>

        </div>

    </div>

</section>

<?php get_footer(); ?>