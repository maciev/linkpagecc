<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;

class AdminPackageUpdate extends Controller {

    public function index() {

        Authentication::guard('admin');

        $package_id = isset($this->params[0]) ? $this->params[0] : false;

        /* Make sure it is either the trial / free package or normal packages */
        switch($package_id) {

            case 'free':

                /* Get the current settings for the free package */
                $package = $this->settings->package_free;

                break;

            case 'trial':

                /* Get the current settings for the trial package */
                $package = $this->settings->package_trial;

                break;

            default:

                $package_id = (int) $package_id;

                /* Check if package exists */
                if(!$package = Database::get('*', 'packages', ['package_id' => $package_id])) {
                    redirect('admin/packages');
                }

                /* Parse the settings of the package */
                $package->settings = json_decode($package->settings);

                break;

        }

        if(!empty($_POST)) {

            if (!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            switch ($_POST['type']) {

                /* Button to update all users package settings with these ones */
                case 'update_users_package_settings':

                    break;

                /* Update the package settings */
                case 'update':

                    /* Filter variables */
                    $_POST['settings'] = [
                        'no_ads'                => (bool) isset($_POST['no_ads']),
                        'removable_branding'    => (bool) isset($_POST['removable_branding']),
                        'custom_branding'       => (bool) isset($_POST['custom_branding']),
                        'custom_colored_links'  => (bool) isset($_POST['custom_colored_links']),
                        'statistics'            => (bool) isset($_POST['statistics']),
                        'google_analytics'      => (bool) isset($_POST['google_analytics']),
                        'facebook_pixel'        => (bool) isset($_POST['facebook_pixel']),
                        'custom_backgrounds'    => (bool) isset($_POST['custom_backgrounds']),
                        'verified'              => (bool) isset($_POST['verified']),
                        'scheduling'            => (bool) isset($_POST['scheduling']),
                        'projects_limit'        => (int) $_POST['projects_limit'],
                        'biolinks_limit'        => (int) $_POST['biolinks_limit'],
                        'links_limit'           => (int) $_POST['links_limit'],
                    ];

                    switch ($package_id) {

                        case 'free':

                            $_POST['name'] = Database::clean_string($_POST['name']);
                            $_POST['is_enabled'] = (int)$_POST['is_enabled'];

                            $setting_key = 'package_free';
                            $setting_value = json_encode([
                                'package_id' => 'free',
                                'name' => $_POST['name'],
                                'days' => $_POST['days'],
                                'is_enabled' => $_POST['is_enabled'],
                                'settings' => $_POST['settings']
                            ]);

                            break;

                        case 'trial':

                            $_POST['name'] = Database::clean_string($_POST['name']);
                            $_POST['days'] = (int)$_POST['days'];
                            $_POST['is_enabled'] = (int)$_POST['is_enabled'];

                            $setting_key = 'package_trial';
                            $setting_value = json_encode([
                                'package_id' => 'trial',
                                'name' => $_POST['name'],
                                'days' => $_POST['days'],
                                'is_enabled' => $_POST['is_enabled'],
                                'settings' => $_POST['settings']
                            ]);

                            break;

                        default:

                            $_POST['name'] = Database::clean_string($_POST['name']);
                            $_POST['monthly_price'] = (float)$_POST['monthly_price'];
                            $_POST['annual_price'] = (float)$_POST['annual_price'];
                            $_POST['is_enabled'] = (int)$_POST['is_enabled'];
                            $_POST['settings'] = json_encode($_POST['settings']);

                            break;

                    }

                    break;
            }


            if (empty($_SESSION['error'])) {

                switch ($_POST['type']) {

                    /* Button to update all users package settings with these ones */
                    case 'update_users_package_settings':

                        $package_settings = json_encode($package->settings);

                        $stmt = Database::$database->prepare("UPDATE `users` SET `package_settings` = ? WHERE `package_id` = ?");
                        $stmt->bind_param('ss', $package_settings, $package_id);
                        $stmt->execute();
                        $stmt->close();

                        break;

                    /* Update the package settings */
                    case 'update':

                        /* Update the database */
                        switch ($package_id) {

                            case 'free':
                            case 'trial':

                                $stmt = Database::$database->prepare("UPDATE `settings` SET `value` = ? WHERE `key` = ?");
                                $stmt->bind_param('ss', $setting_value, $setting_key);
                                $stmt->execute();
                                $stmt->close();

                                break;

                            default:

                                $stmt = Database::$database->prepare("UPDATE `packages` SET `name` = ?, `monthly_price` = ?, `annual_price` = ?, `settings` = ?, `is_enabled` = ? WHERE `package_id` = ?");
                                $stmt->bind_param('ssssss', $_POST['name'], $_POST['monthly_price'], $_POST['annual_price'], $_POST['settings'], $_POST['is_enabled'], $package_id);
                                $stmt->execute();
                                $stmt->close();

                                break;

                        }

                }

                /* Set a nice success message */
                $_SESSION['success'][] = $this->language->global->success_message->basic;

                /* Refresh the page */
                redirect('admin/package-update/' . $package_id);

            }

        }


        /* Main View */
        $data = [
            'package_id'    => $package_id,
            'package'       => $package,
        ];

        $view = new \Altum\Views\View('admin/package-update/index', (array) $this);

        $this->addViewContent('content', $view->run($data));

    }

}
