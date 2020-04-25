<?php

namespace Altum\Controllers;

use Altum\Captcha;
use Altum\Database\Database;
use Altum\Language;
use Altum\Logger;
use Altum\Middlewares\Authentication;

class Register extends Controller {

    public function index() {

        /* Check if Registration is enabled first */
        if(!$this->settings->register_is_enabled) {
            redirect();
        }

        Authentication::guard('guest');

        $redirect = 'dashboard';
        if(isset($_GET['redirect']) && $redirect = $_GET['redirect']) {
            $redirect = Database::clean_string($redirect);
        }

        /* Default variables */
        $values = [
            'name' => '',
            'email' => '',
            'password' => ''
        ];

        /* Initiate captcha */
        $captcha = new Captcha([
            'recaptcha' => $this->settings->captcha->recaptcha_is_enabled,
            'recaptcha_public_key' => $this->settings->captcha->recaptcha_public_key,
            'recaptcha_private_key' => $this->settings->captcha->recaptcha_private_key
        ]);

        if(!empty($_POST)) {
            /* Clean some posted variables */
            $_POST['name']		= string_filter_alphanumeric(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
            $_POST['email']		= filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            /* Default variables */
            $values['name'] = $_POST['name'];
            $values['email'] = $_POST['email'];
            $values['password'] = $_POST['password'];

            /* Define some variables */
            $fields = ['name', 'email' ,'password'];

            /* Check for any errors */
            foreach($_POST as $key => $value) {
                if(empty($value) && in_array($key, $fields) == true) {
                    $_SESSION['error'][] = $this->language->global->error_message->empty_fields;
                    break 1;
                }
            }
            if(!$captcha->is_valid()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_captcha;
            }
            if(strlen($_POST['name']) < 3 || strlen($_POST['name']) > 32) {
                $_SESSION['error'][] = $this->language->register->error_message->name_length;
            }
            if(Database::exists('user_id', 'users', ['email' => $_POST['email']])) {
                $_SESSION['error'][] = $this->language->register->error_message->email_exists;
            }
            if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'][] = $this->language->register->error_message->invalid_email;
            }
            if(strlen(trim($_POST['password'])) < 6) {
                $_SESSION['error'][] = $this->language->register->error_message->short_password;
            }

            /* If there are no errors continue the registering process */
            if(empty($_SESSION['error'])) {
                /* Define some needed variables */
                $password                   = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $active 	                = (int) !$this->settings->email_confirmation;
                $email_code                 = md5($_POST['email'] . microtime());
                $last_user_agent            = Database::clean_string($_SERVER['HTTP_USER_AGENT']);
                $total_logins               = $active == '1' ? 1 : 0;
                $package_id                 = 'free';
                $package_expiration_date    = \Altum\Date::get();
                $ip                         = get_ip();
                $package_settings           = json_encode($this->settings->package_free->settings);

                /* Add the user to the database */
                $stmt = Database::$database->prepare("INSERT INTO `users` (`password`, `email`, `email_activation_code`, `name`, `package_id`, `package_expiration_date`, `package_settings`, `language`, `active`, `date`, `ip`, `last_user_agent`, `total_logins`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('sssssssssssss', $password, $_POST['email'], $email_code, $_POST['name'], $package_id, $package_expiration_date, $package_settings, Language::$language, $active, \Altum\Date::$date, $ip, $last_user_agent, $total_logins);
                $stmt->execute();
                $registered_user_id = $stmt->insert_id;
                $stmt->close();

                /* Log the action */
                Logger::users($registered_user_id, 'register.register');

                /* Send notification to admin if needed */
                if($this->settings->email_notifications->new_user && !empty($this->settings->email_notifications->emails)) {

                    send_mail(
                        $this->settings,
                        explode(',', $this->settings->email_notifications->emails),
                        $this->language->global->email_notifications->new_user_subject,
                        sprintf($this->language->global->email_notifications->new_user_body, $_POST['name'], $_POST['email'])
                    );

                }

                /* If active = 1 then login the user, else send the user an activation email */
                if($active == '1') {
                    $_SESSION['user_id'] = $registered_user_id;
                    $_SESSION['success'] = $this->language->register->success_message->login;

                    Logger::users($registered_user_id, 'login.success');

                    redirect($redirect);
                } else {

                    /* Prepare the email */
                    $email_template = get_email_template(
                        [
                            '{{NAME}}' => $_POST['name'],
                        ],
                        $this->language->global->emails->account_activation->subject,
                        [
                            '{{ACTIVATION_LINK}}' => url('activate-user/' . md5($_POST['email']) . '/' . $email_code . '?redirect=' . $redirect),
                            '{{NAME}}' => $_POST['name'],
                        ],
                        $this->language->global->emails->account_activation->body
                    );

                    send_mail($this->settings, $_POST['email'], $email_template->subject, $email_template->body);

                    $_SESSION['success'][] = $this->language->register->success_message->registration;
                }

            }
        }

        /* Main View */
        $data = [
            'values' => $values,
            'captcha' => $captcha
        ];

        $view = new \Altum\Views\View('register/index', (array) $this);

        $this->addViewContent('content', $view->run($data));

    }

}
