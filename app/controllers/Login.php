<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Logger;
use Altum\Middlewares\Authentication;

class Login extends Controller {

    public function index() {

        Authentication::guard('guest');

        $method	= (isset($this->params[0])) ? $this->params[0] : false;
        $redirect = 'dashboard';

        if(isset($_GET['redirect']) && $redirect = $_GET['redirect']) {
            // Nothing for now.
        }

        /* Default values */
        $values = [
            'email' => ''
        ];

        /* Instagram Login / Register */
        if($this->settings->instagram->is_enabled && !empty($this->settings->instagram->client_id) && !empty($this->settings->instagram->client_secret)) {

            $instagram = new \MetzWeb\Instagram\Instagram([
                'apiKey'      => $this->settings->instagram->client_id,
                'apiSecret'   => $this->settings->instagram->client_secret,
                'apiCallback' => url('login/instagram')
            ]);

            $instagram_login_url = $instagram->getLoginUrl();

            if($method == 'instagram') {
                $instagram_data = $instagram->getOAuthToken($_GET['code']);

                if(isset($instagram_data->error_message)) {
                    $_SESSION['error'][] = 'Instagram Auth Error: ' . $instagram_data->error_message;
                }

                if(empty($_SESSION['error'])) {

                    /* If the user is already in the system, log him in */
                    if ($user = Database::get(['user_id'], 'users', ['instagram_id' => $instagram_data->user->id])) {
                        $_SESSION['user_id'] = $user->user_id;

                        redirect('dashboard');
                    }

                    /* Create a new account */
                    else {
                        /* Generate a random username */
                        $email = get_slug($instagram_data->user->username);

                        /* If the user already exists, generate a new username with some random characters */
                        while(Database::exists('email', 'users', ['username' => $email])) {
                            $email = get_slug($instagram_data->user->username) . rand(100, 999);
                        }

                        if (empty($_SESSION['error'])) {
                            $generated_password = string_generate(8);
                            $password = password_hash($generated_password, PASSWORD_DEFAULT);
                            $name = $instagram_data->user->full_name;
                            $active                     = 1;
                            $last_user_agent            = Database::clean_string($_SERVER['HTTP_USER_AGENT']);
                            $total_logins               = 1;
                            $package_id                 = 'free';
                            $package_expiration_date    = \Altum\Date::get();
                            $package_settings           = json_encode($this->settings->package_free->settings);
                            $ip                         = get_ip();

                            /* Add the user to the database */
                            $stmt = Database::$database->prepare("INSERT INTO `users` (`password`, `email`, `name`, `instagram_id`, `package_id`, `package_expiration_date`, `package_settings`, `active`, `date`, `ip`, `last_user_agent`, `total_logins`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param('ssssssssssss', $password, $email, $name, $instagram_data->user->id, $package_id, $package_expiration_date, $package_settings, $active, \Altum\Date::$date, $ip, $last_user_agent, $total_logins);
                            $stmt->execute();
                            $registered_user_id = $stmt->insert_id;
                            $stmt->close();

                            /* Log the action */
                            Logger::users($registered_user_id, 'register.instagram_register');

                            /* Send notification to admin if needed */
                            if($this->settings->email_notifications->new_user && !empty($this->settings->email_notifications->emails)) {

                                send_mail(
                                    $this->settings,
                                    explode(',', $this->settings->email_notifications->emails),
                                    $this->language->global->email_notifications->new_user_subject,
                                    sprintf($this->language->global->email_notifications->new_user_body, $name, $email)
                                );

                            }

                            /* Log the user in and redirect him */
                            $_SESSION['user_id'] = $registered_user_id;
                            $_SESSION['success'][] = $this->language->register->success_message->login;

                            Logger::users($registered_user_id, 'login.success');

                            redirect($redirect);
                        }
                    }
                }

            }
        }

        /* Facebook Login / Register */
        if($this->settings->facebook->is_enabled && !empty($this->settings->facebook->app_id) && !empty($this->settings->facebook->app_secret)) {

            $facebook = new \Facebook\Facebook([
                'app_id' => $this->settings->facebook->app_id,
                'app_secret' => $this->settings->facebook->app_secret,
                'default_graph_version' => 'v3.2',
            ]);

            $facebook_helper = $facebook->getRedirectLoginHelper();
            $facebook_login_url = $facebook->getRedirectLoginHelper()->getLoginUrl(url('login/facebook'), ['email', 'public_profile']);

            /* Check for the redirect after the oauth checkin */
            if($method == 'facebook') {
                try {
                    $facebook_access_token = $facebook_helper->getAccessToken(url('login/facebook'));
                } catch(Facebook\Exceptions\FacebookResponseException $e) {
                    $_SESSION['error'][] = 'Graph returned an error: ' . $e->getMessage();
                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                    $_SESSION['error'][] = 'Facebook SDK returned an error: ' . $e->getMessage();
                }
            }

            if(isset($facebook_access_token)) {

                /* The OAuth 2.0 client handler helps us manage access tokens */
                $facebook_oAuth2_client = $facebook->getOAuth2Client();

                /* Get the access token metadata from /debug_token */
                $facebook_token_metadata = $facebook_oAuth2_client->debugToken($facebook_access_token);

                /* Validation */
                $facebook_token_metadata->validateAppId($this->settings->facebook->app_id);
                $facebook_token_metadata->validateExpiration();

                if(!$facebook_access_token->isLongLived()) {
                    /* Exchanges a short-lived access token for a long-lived one */
                    try {
                        $facebook_access_token = $facebook_oAuth2_client->getLongLivedAccessToken($facebook_access_token);
                    } catch (Facebook\Exceptions\FacebookSDKException $e) {
                        $_SESSION['error'][] = 'Error getting long-lived access token: ' . $facebook_helper->getMessage();
                    }
                }

                try {
                    $response = $facebook->get('/me?fields=id,name,email', $facebook_access_token);
                } catch(Facebook\Exceptions\FacebookResponseException $e) {
                    $_SESSION['error'][] = 'Graph returned an error: ' . $e->getMessage();
                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                    $_SESSION['error'][] = 'Facebook SDK returned an error: ' . $e->getMessage();
                }

                if(isset($response)) {
                    $facebook_user = $response->getGraphUser();
                    $facebook_user_id = $facebook_user->getId();
                    $email = $facebook_user->getEmail();

                    /* Check if email is actually not null */
                    if(is_null($email)) {
                        $_SESSION['error'][] = $this->language->login->error_message->email_is_null;

                        redirect('login');
                    }

                    /* If the user is already in the system, log him in */
                    if($user = Database::get(['user_id'], 'users', ['email' => $email])) {
                        $_SESSION['user_id'] = $user->user_id;

                        redirect($redirect);
                    }

                    /* Create a new account */
                    else {

                        if(empty($_SESSION['error'])) {
                            $password                   = password_hash(string_generate(8), PASSWORD_DEFAULT);
                            $name                       = $facebook_user->getName();
                            $active                     = 1;
                            $last_user_agent            = Database::clean_string($_SERVER['HTTP_USER_AGENT']);
                            $total_logins               = 1;
                            $package_id                 = 'free';
                            $package_expiration_date    = \Altum\Date::get();
                            $package_settings           = json_encode($this->settings->package_free->settings);
                            $ip                         = get_ip();

                            /* Add the user to the database */
                            $stmt = Database::$database->prepare("INSERT INTO `users` (`password`, `email`, `name`, `facebook_id`, `package_id`, `package_expiration_date`, `package_settings`, `active`, `date`, `ip`, `last_user_agent`, `total_logins`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param('ssssssssssss', $password, $email, $name, $facebook_user_id, $package_id, $package_expiration_date, $package_settings, $active, \Altum\Date::$date, $ip, $last_user_agent, $total_logins);
                            $stmt->execute();
                            $registered_user_id = $stmt->insert_id;
                            $stmt->close();

                            /* Log the action */
                            Logger::users($registered_user_id, 'register.facebook_register');

                            /* Send notification to admin if needed */
                            if($this->settings->email_notifications->new_user && !empty($this->settings->email_notifications->emails)) {

                                send_mail(
                                    $this->settings,
                                    explode(',', $this->settings->email_notifications->emails),
                                    $this->language->global->email_notifications->new_user_subject,
                                    sprintf($this->language->global->email_notifications->new_user_body, $name, $email)
                                );

                            }

                            /* Log the user in and redirect him */
                            $_SESSION['user_id'] = $registered_user_id;
                            $_SESSION['success'][] = $this->language->register->success_message->login;

                            Logger::users($registered_user_id, 'login.success');

                            redirect($redirect);
                        }
                    }
                }
            }
        }

        if(!empty($_POST)) {
            /* Clean email and encrypt the password */
            $_POST['email'] = Database::clean_string($_POST['email']);
            $values['email'] = $_POST['email'];

            /* Check for any errors */
            if(empty($_POST['email']) || empty($_POST['password'])) {
                $_SESSION['error'][] = $this->language->global->error_message->empty_fields;
            }

            /* Try to get the user from the database */
            $result = Database::$database->query("SELECT `user_id`, `email`, `active`, `password`, `token_code`, `total_logins` FROM `users` WHERE `email` = '{$_POST['email']}'");
            $login_account = $result->num_rows ? $result->fetch_object() : false;

            if(!$login_account) {
                $_SESSION['error'][] = $this->language->login->error_message->wrong_login_credentials;
            } else {

                if(!$login_account->active) {
                    $_SESSION['error'][] = $this->language->login->error_message->user_not_active;
                } else

                    if(!password_verify($_POST['password'], $login_account->password)) {
                        Logger::users($login_account->user_id, 'login.wrong_password');

                        $_SESSION['error'][] = $this->language->login->error_message->wrong_login_credentials;
                    }

            }

            if(empty($_SESSION['error'])) {
                /* If remember me is checked, log the user with cookies for 30 days else, remember just with a session */
                if(isset($_POST['rememberme'])) {
                    $token_code = $login_account->token_code;

                    /* Generate a new token */
                    if(empty($login_account->token_code)) {
                        $token_code = md5($login_account->email . microtime());

                        Database::update('users', ['token_code' => $token_code], ['user_id' => $login_account->user_id]);
                    }

                    setcookie('email', $login_account->email, time()+60*60*24*30);
                    setcookie('token_code', $token_code, time()+60*60*24*30);

                } else {
                    $_SESSION['user_id'] = $login_account->user_id;
                }

                $user_agent = Database::clean_string($_SERVER['HTTP_USER_AGENT']);
                Database::update('users', [
                    'last_user_agent'   => $user_agent,
                    'total_logins'      => $login_account->total_logins + 1
                ], ['user_id' => $login_account->user_id]);

                Logger::users($login_account->user_id, 'login.success');

                $_SESSION['info'][] = $this->language->login->info_message->logged_in;
                redirect($redirect);
            }
        }

        /* Prepare the View */
        $data = [
            'values' => $values,
            'facebook_login_url' => $facebook_login_url ?? false,
            'instagram_login_url' => $instagram_login_url ?? false
        ];

        $view = new \Altum\Views\View('login/index', (array) $this);

        $this->addViewContent('content', $view->run($data));

    }

}
