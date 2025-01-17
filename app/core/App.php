<?php

namespace Altum;

use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Models\Package;
use Altum\Models\User;
use \Altum\Routing\Router;
use \Altum\Models\Settings;

class App {

    protected $database;

    public function __construct() {

        /* Connect to the database */
        $this->database = Database\Database::initialize();

        /* Parse the URL parameters */
        Router::parse_url();

        /* Handle the controller */
        Router::parse_controller();

        /* Create a new instance of the controller */
        $controller = Router::get_controller(Router::$controller, Router::$path);

        /* Process the method and get it */
        $method = Router::parse_method($controller);

        /* Get the remaining params */
        $params = Router::get_params();

        /* Get the website settings */
        $settings = (new Settings())->get();

        /* Initiate the Language system */
        Language::initialize(APP_PATH . 'languages/', $settings->default_language);

        /* Get the needed language strings */
        $language = Language::get();

        /* Initiate the Title system */
        Title::initialize($settings->title);

        /* Set the date timezone */
        date_default_timezone_set($settings->time_zone);

        /* Set locale */
        setlocale(LC_ALL, $language->global->date->locale);

        /* Check for a potential logged in account and do some extra checks */
        if(Authentication::check()) {

            $user = (new User())->get(Authentication::$user_id);

            if(!$user) {
                Authentication::logout();
            }

            $user_id = Authentication::$user_id;

            /* Determine if the current package is expired or disabled */
            $user->package_is_expired = false;

            /* Get current package proper details */
            $user->package = (new Package(['settings' => $settings]))->get_package_by_id($user->package_id);

            /* Check if its a custom package */
            if($user->package->package_id == 'custom') {
                $user->package->settings = $user->package_settings;
            }

            if(!$user->package || ($user->package && ((new \DateTime()) > (new \DateTime($user->package_expiration_date)) && $user->package_id != 'free') || !$user->package->is_enabled)) {
                $user->package_is_expired = true;

                /* If the free package is available, give it to the user */
                if($settings->package_free->is_enabled) {
                    $package_settings = json_encode($settings->package_free->settings);

                    $this->database->query("UPDATE `users` SET `package_id` = 'free', `package_settings` = '{$package_settings}' WHERE `user_id` = {$user_id}");
                }

                /* Make sure we delete the subscription_id if any */
                if($user->payment_subscription_id) {
                    $this->database->query("UPDATE `users` SET `payment_subscription_id` = '' WHERE `user_id` = {$user_id}");
                }

                /* Make sure to redirect the person to the payment page and only let the person access the following pages */
                if(!in_array(Router::$controller_key, ['package', 'pay', 'account', 'logout', 'page']) && Router::$path != 'admin') {
                    redirect('package/new');
                }
            }

            /* Update last activity */
            if((new \DateTime())->modify('+5 minutes') < (new \DateTime($user->last_activity))) {
                (new User())->update_last_activity(Authentication::$user_id);
            }

            /* Update the language of the site for next page use if the current language (default) is different than the one the user has */
            if(Language::$language != $user->language) {
                Language::set($user->language);
            }

            /* Update the language of the user if needed */
            if(isset($_GET['language']) && in_array($_GET['language'], Language::$languages)) {
                $this->database->query("UPDATE `users` SET `language` = '{$_GET['language']}' WHERE `user_id` = {$user_id}");
            }

            /* Store all the details of the user in the Authentication static class as well */
            Authentication::$user = $user;
        }

        /* Set a CSRF Token */
        Csrf::set('token');
        Csrf::set('global_token');

        /* Setting the datetime for backend usages ( insertions in database..etc ) */
        Date::$date = Date::get();

        /* Add main vars inside of the controller */
        $controller->add_params([
            'database'  => $this->database,
            'params'    => $params,
            'settings'  => $settings,
            'language'  => $language,

            /* Potential logged in user */
            'user'      => Authentication::$user
        ]);

        /* Call the controller method */
        call_user_func_array([ $controller, $method ], []);

        /* Render and output everything */
        $controller->run();
    }

}
