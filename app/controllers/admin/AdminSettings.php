<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;

class AdminSettings extends Controller {

    public function index() {

        Authentication::guard('admin');

        if(!empty($_POST)) {
            /* Define some variables */
            $image_allowed_extensions = ['jpg', 'jpeg', 'png', 'svg', 'ico'];

            /* Main Tab */
            $_POST['title'] = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
            $_POST['time_zone'] = filter_var($_POST['time_zone'], FILTER_SANITIZE_STRING);
            $logo = (!empty($_FILES['logo']['name']));
            $logo_name = $logo ? '' : $this->settings->logo;
            $favicon = (!empty($_FILES['favicon']['name']));
            $favicon_name = $favicon ? '' : $this->settings->favicon;
            $_POST['email_confirmation'] = (bool) $_POST['email_confirmation'];
            $_POST['register_is_enabled'] = (bool) $_POST['register_is_enabled'];
            $_POST['terms_and_conditions_url'] = filter_var($_POST['terms_and_conditions_url'], FILTER_SANITIZE_STRING);
            $_POST['privacy_policy_url'] = filter_var($_POST['privacy_policy_url'], FILTER_SANITIZE_STRING);

            /* Links Tab */
            $_POST['links_blacklisted_domains'] = array_map('trim', explode(',', $_POST['links_blacklisted_domains']));
            $_POST['links_blacklisted_keywords'] = array_map('trim', explode(',', $_POST['links_blacklisted_keywords']));
            $_POST['links_phishtank_is_enabled'] = (bool) $_POST['links_phishtank_is_enabled'];
            $_POST['links_google_safe_browsing_is_enabled'] = (bool) $_POST['links_google_safe_browsing_is_enabled'];

            /* Payment Tab */
            $_POST['payment_is_enabled'] = (bool) $_POST['payment_is_enabled'];
            $_POST['paypal_is_enabled'] = (bool) $_POST['paypal_is_enabled'];
            $_POST['stripe_is_enabled'] = (bool) $_POST['stripe_is_enabled'];

            /* Business Tab */
            $_POST['business_invoice_is_enabled'] = (bool) $_POST['business_invoice_is_enabled'];

            /* Facebook Tab */
            $_POST['facebook_is_enabled'] = (bool) $_POST['facebook_is_enabled'];

            /* Instagram Tab */
            $_POST['instagram_is_enabled'] = (bool) $_POST['instagram_is_enabled'];


            /* SMTP Tab */
            $_POST['smtp_auth'] = (bool) isset($_POST['smtp_auth']);
            $_POST['smtp_username'] = filter_var($_POST['smtp_username'] ?? '', FILTER_SANITIZE_STRING);
            $_POST['smtp_password'] = $_POST['smtp_password'] ?? '';


            /* Email notifications */
            $_POST['email_notifications_emails'] = str_replace(' ', '', $_POST['email_notifications_emails']);
            $_POST['email_notifications_new_user'] = (bool) isset($_POST['email_notifications_new_user']);
            $_POST['email_notifications_new_payment'] = (bool) isset($_POST['email_notifications_new_payment']);


            /* Check for any errors on the logo image */
            if($logo) {
                $logo_file_name = $_FILES['logo']['name'];
                $logo_file_extension = explode('.', $logo_file_name);
                $logo_file_extension = strtolower(end($logo_file_extension));
                $logo_file_temp = $_FILES['logo']['tmp_name'];
                $logo_file_size = $_FILES['logo']['size'];
                list($logo_width, $logo_height) = getimagesize($logo_file_temp);

                if(!in_array($logo_file_extension, $image_allowed_extensions)) {
                    $_SESSION['error'][] = $this->language->global->error_message->invalid_file_type;
                }

                if(!is_writable(UPLOADS_PATH . 'logo/')) {
                    $_SESSION['error'][] = sprintf($this->language->global->error_message->directory_not_writable, UPLOADS_PATH . 'logo/');
                }

                if(empty($_SESSION['error'])) {

                    /* Delete current logo */
                    if(!empty($this->settings->logo) && file_exists(UPLOADS_PATH . 'logo/' . $this->settings->logo)) {
                        unlink(UPLOADS_PATH . 'logo/' . $this->settings->logo);
                    }

                    /* Generate new name for logo */
                    $logo_new_name = md5(time() . rand()) . '.' . $logo_file_extension;

                    /* Upload the original */
                    move_uploaded_file($logo_file_temp, UPLOADS_PATH . 'logo/' . $logo_new_name);

                    /* Execute query */
                    Database::$database->query("UPDATE `settings` SET `value` = '{$logo_new_name}' WHERE `key` = 'logo'");

                }
            }

            /* Check for any errors on the logo image */
            if($favicon) {
                $favicon_file_name = $_FILES['favicon']['name'];
                $favicon_file_extension = explode('.', $favicon_file_name);
                $favicon_file_extension = strtolower(end($favicon_file_extension));
                $favicon_file_temp = $_FILES['favicon']['tmp_name'];
                $favicon_file_size = $_FILES['favicon']['size'];
                list($favicon_width, $favicon_height) = getimagesize($favicon_file_temp);

                if(!in_array($favicon_file_extension, $image_allowed_extensions)) {
                    $_SESSION['error'][] = $this->language->global->error_message->invalid_file_type;
                }

                if(!is_writable(UPLOADS_PATH . 'favicon/')) {
                    $_SESSION['error'][] = sprintf($this->language->global->error_message->directory_not_writable, UPLOADS_PATH . 'favicon/');
                }

                if(empty($_SESSION['error'])) {

                    /* Delete current favicon */
                    if(!empty($this->settings->favicon) && file_exists(UPLOADS_PATH . 'favicon/' . $this->settings->favicon)) {
                        unlink(UPLOADS_PATH . 'favicon/' . $this->settings->favicon);
                    }

                    /* Generate new name for favicon */
                    $favicon_new_name = md5(time() . rand()) . '.' . $favicon_file_extension;

                    /* Upload the original */
                    move_uploaded_file($favicon_file_temp, UPLOADS_PATH . 'favicon/' . $favicon_new_name);

                    /* Execute query */
                    Database::$database->query("UPDATE `settings` SET `value` = '{$favicon_new_name}' WHERE `key` = 'favicon'");

                }
            }

            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(empty($_SESSION['error'])) {

                $settings_keys = [

                    /* Main */
                    'title',
                    'default_language',
                    'time_zone',
                    'email_confirmation',
                    'register_is_enabled',
                    'index_url',
                    'terms_and_conditions_url',
                    'privacy_policy_url',

                    /* Links */
                    'links' => [
                        'blacklisted_domains',
                        'blacklisted_keywords',
                        'phishtank_is_enabled',
                        'phishtank_api_key',
                        'google_safe_browsing_is_enabled',
                        'google_safe_browsing_api_key'
                    ],

                    /* Payment */
                    'payment' => [
                        'is_enabled',
                        'brand_name',
                        'currency'
                    ],

                    'paypal' => [
                        'is_enabled',
                        'mode',
                        'client_id',
                        'secret'
                    ],

                    'stripe' => [
                        'is_enabled',
                        'publishable_key',
                        'secret_key',
                        'webhook_secret'
                    ],

                    /* Business */
                    'business' => [
                        'invoice_is_enabled',
                        'name',
                        'address',
                        'city',
                        'county',
                        'zip',
                        'country',
                        'email',
                        'phone',
                        'tax_type',
                        'tax_id'
                    ],

                    /* Captcha */
                    'captcha' => [
                        'recaptcha_is_enabled',
                        'recaptcha_public_key',
                        'recaptcha_private_key'
                    ],

                    /* Facebook */
                    'facebook' => [
                        'is_enabled',
                        'app_id',
                        'app_secret'
                    ],

                    /* Instagram */
                    'instagram' => [
                        'is_enabled',
                        'client_id',
                        'client_secret'
                    ],

                    /* Ads */
                    'ads' => [
                        'header',
                        'footer'
                    ],

                    /* Socials */
                    'socials' => [
                        'facebook',
                        'instagram',
                        'twitter',
                        'youtube'
                    ],

                    /* SMTP */
                    'smtp' => [
                        'host',
                        'from',
                        'encryption',
                        'port',
                        'auth',
                        'username',
                        'password'
                    ],

                    /* Custom */
                    'custom' => [
                        'head_js',
                        'head_css'
                    ],

                    /* Email Notifications */
                    'email_notifications' => [
                        'emails',
                        'new_user',
                        'new_payment'
                    ]
                ];

                /* Go over each key and make sure to update it accordingly */
                foreach ($settings_keys as $key => $value) {

                    if(is_array($value)) {

                        $values_array = [];

                        foreach ($value as $sub_key) {

                            /* Check if the field needs cleaning */
                            if(!in_array($key . '_' . $sub_key, ['links_blacklisted_domains', 'links_blacklisted_keywords', 'custom_head_css', 'custom_head_js', 'ads_header', 'ads_footer'])) {
                                $values_array[$sub_key] = Database::clean_string($_POST[$key . '_' . $sub_key]);
                            } else {
                                $values_array[$sub_key] = $_POST[$key . '_' . $sub_key];
                            }
                        }

                        $value = json_encode($values_array);

                    } else {
                        $key = $value;
                        $value = $_POST[$key];
                    }

                    $stmt = Database::$database->prepare("UPDATE `settings` SET `value` = ? WHERE `key` = ?");
                    $stmt->bind_param('ss', $value, $key);
                    $stmt->execute();
                    $stmt->close();

                }

                /* Set message */
                $_SESSION['success'][] = $this->language->admin_settings->success_message->saved;

                /* Refresh the page */
                redirect('admin/settings');

            }
        }

        /* Main View */
        $view = new \Altum\Views\View('admin/settings/index', (array) $this);

        $this->addViewContent('content', $view->run());

    }

    public function removelogo() {

        Authentication::guard('admin');

        if(!Csrf::check()) {
            redirect('admin/settings');
        }

        /* Delete the current logo */
        if(file_exists(UPLOADS_PATH . 'logo/' . $this->settings->logo)) {
            unlink(UPLOADS_PATH . 'logo/' . $this->settings->logo);
        }

        /* Remove it from db */
        Database::$database->query("UPDATE `settings` SET `value` = '' WHERE `key` = 'logo'");

        /* Set message & Redirect */
        $_SESSION['success'][] = $this->language->global->success_message->basic;
        redirect('admin/settings');

    }

    public function removefavicon() {

        Authentication::guard('admin');

        if(!Csrf::check()) {
            redirect('admin/settings');
        }

        /* Delete the current logo */
        if(file_exists(UPLOADS_PATH . 'favicon/' . $this->settings->favicon)) {
            unlink(UPLOADS_PATH . 'favicon/' . $this->settings->favicon);
        }

        /* Remove it from db */
        Database::$database->query("UPDATE `settings` SET `value` = '' WHERE `key` = 'favicon'");

        /* Set message & Redirect */
        $_SESSION['success'][] = $this->language->global->success_message->basic;
        redirect('admin/settings');

    }

    public function testemail() {

        Authentication::guard('admin');

        if(!Csrf::check()) {
            redirect('admin/settings');
        }

        $result = send_mail($this->settings, $this->settings->smtp->from, $this->settings->title . ' - Test Email', 'This is just a test email to confirm the smtp email settings!', true);

        if($result->ErrorInfo == '') {
            $_SESSION['success'][] = $this->language->admin_settings->success_message->email;
        } else {
            $_SESSION['error'][] = sprintf($this->language->admin_settings->error_message->email, $result->ErrorInfo);
        }

        redirect('admin/settings');
    }
}
