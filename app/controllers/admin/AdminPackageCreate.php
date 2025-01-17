<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;

class AdminPackageCreate extends Controller {

    public function index() {

        Authentication::guard('admin');

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['name'] = Database::clean_string($_POST['name']);
            $_POST['monthly_price'] = (float) $_POST['monthly_price'];
            $_POST['annual_price'] = (float) $_POST['annual_price'];

            $_POST['settings'] = json_encode([
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
            ]);
            $_POST['is_enabled'] = (int) $_POST['is_enabled'];

            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(empty($_SESSION['error'])) {
                /* Update the database */
                $stmt = Database::$database->prepare("INSERT INTO `packages` (`name`, `monthly_price`, `annual_price`, `settings`, `is_enabled`, `date`) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssss', $_POST['name'], $_POST['monthly_price'], $_POST['annual_price'], $_POST['settings'], $_POST['is_enabled'], Date::$date);
                $stmt->execute();
                $stmt->close();

                /* Set a nice success message */
                $_SESSION['success'][] = $this->language->global->success_message->basic;

                redirect('admin/packages');
            }
        }


        /* Main View */
        $data = [
        ];

        $view = new \Altum\Views\View('admin/package-create/index', (array) $this);

        $this->addViewContent('content', $view->run($data));

    }

}
