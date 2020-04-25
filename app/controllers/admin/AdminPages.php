<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Models\Package;
use Altum\Models\User;
use Altum\Middlewares\Authentication;
use Altum\Response;
use Altum\Routing\Router;

class AdminPages extends Controller {

    public function index() {

        Authentication::guard('admin');

        $pages_result = Database::$database->query("SELECT * FROM `pages` ORDER BY `page_id` ASC");

        /* Main View */
        $data = [
            'pages_result' => $pages_result
        ];

        $view = new \Altum\Views\View('admin/pages/index', (array) $this);

        $this->addViewContent('content', $view->run($data));

    }

    public function delete() {

        Authentication::guard();

        $page_id = (isset($this->params[0])) ? $this->params[0] : false;

        if(!Csrf::check()) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
        }

        if(empty($_SESSION['error'])) {

            /* Delete the page */
            Database::$database->query("DELETE FROM `pages` WHERE `page_id` = {$page_id}");

            redirect('admin/pages');

        }

        die();
    }

}
