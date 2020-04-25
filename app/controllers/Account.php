<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Models\User;

class Account extends Controller {

    public function index() {

        Authentication::guard();

        if(!empty($_POST)) {

            /* Clean some posted variables */
            $_POST['email']		= filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $_POST['name']		= string_filter_alphanumeric(filter_var($_POST['name'], FILTER_SANITIZE_STRING));

            /* Check for any errors */
            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }
            if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
                $_SESSION['error'][] = $this->language->register->error_message->invalid_email;
            }
            if(Database::exists('user_id', 'users', ['email' => $_POST['email']]) && $_POST['email'] !== $this->user->email) {
                $_SESSION['error'][] = $this->language->register->error_message->email_exists;
            }

            if(strlen($_POST['name']) < 3 || strlen($_POST['name'] > 32)) {
                $_SESSION['error'][] = $this->language->register->error_message->name_length;
            }

            if(!empty($_POST['old_password']) && !empty($_POST['new_password'])) {
                if(!password_verify($_POST['old_password'], $this->user->password)) {
                    $_SESSION['error'][] = $this->language->account->error_message->invalid_current_password;
                }
                if(strlen(trim($_POST['new_password'])) < 6) {
                    $_SESSION['error'][] = $this->language->account->error_message->short_password;
                }
                if($_POST['new_password'] !== $_POST['repeat_password']) {
                    $_SESSION['error'][] = $this->language->account->error_message->passwords_not_matching;
                }
            }

            if(empty($_SESSION['error'])) {

                /* Prepare the statement and execute query */
                $stmt = Database::$database->prepare("UPDATE `users` SET `email` = ?, `name` = ? WHERE `user_id` = {$this->user->user_id}");
                $stmt->bind_param('ss', $_POST['email'], $_POST['name']);
                $stmt->execute();
                $stmt->close();

                $_SESSION['success'][] = $this->language->account->success_message->account_updated;

                if(!empty($_POST['old_password']) && !empty($_POST['new_password'])) {
                    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

                    Database::update('users', ['password' => $new_password], ['user_id' => $this->user->user_id]);

                    /* Set a success message and log out the user */
                    Authentication::logout();
                }

                redirect('account');
            }

        }

        /* Prepare the View */
        $view = new \Altum\Views\View('account/index', (array) $this);

        $this->addViewContent('content', $view->run());

    }

    public function delete() {

        Authentication::guard();

        if(!Csrf::check()) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
        }

        if(empty($_SESSION['error'])) {

            /* Delete the user */
            (new User(['settings' => $this->settings]))->delete($this->user->user_id);
            Authentication::logout();

        }

    }

    public function cancelsubscription() {

        Authentication::guard();

        if(!Csrf::check()) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            redirect('account');
        }

        if(empty($_SESSION['error'])) {

            try {
                (new User(['settings' => $this->settings, 'user' => $this->user]))->cancel_subscription();
            } catch (\Exception $exception) {

                /* Output errors properly */
                if (DEBUG) {
                    echo $exception->getCode() . '-' . $exception->getMessage();

                    die();
                } else {

                    $_SESSION['error'][] = $exception->getMessage();
                    redirect('account');

                }
            }

            /* Set a message */
            $_SESSION['success'][] = $this->language->account->success_message->subscription_canceled;

            redirect('account');

        }

    }

}
