<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Models\Package;
use Altum\Models\User;
use Altum\Middlewares\Authentication;
use Altum\Response;
use Altum\Routing\Router;

class AdminPageCreate extends Controller {

    public function index() {

        Authentication::guard('admin');

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['title'] = Database::clean_string($_POST['title']);
            $_POST['type'] = in_array($_POST['type'], ['INTERNAL', 'EXTERNAL']) ? Database::clean_string($_POST['type']) : 'INTERNAL';
            $_POST['position'] = in_array($_POST['position'], ['1', '0']) ? $_POST['position'] : '0';

            switch($_POST['type']) {
                case 'INTERNAL':
                    $_POST['url'] = get_slug(Database::clean_string($_POST['url']), '-');
                    break;


                case 'EXTERNAL':
                    $_POST['url'] = Database::clean_string($_POST['url']);
                    break;
            }

            $required_fields = ['title', 'url'];

            /* Check for the required fields */
            foreach($_POST as $key => $value) {
                if(empty($value) && in_array($key, $required_fields)) {
                    $_SESSION['error'][] = $this->language->global->error_message->empty_fields;
                    break 1;
                }
            }

            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            /* If there are no errors continue the updating process */
            if(empty($_SESSION['error'])) {
                $stmt = Database::$database->prepare("INSERT INTO `pages` (`title`, `url`, `description`, `type`, `position`) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('sssss', $_POST['title'], $_POST['url'], $_POST['description'], $_POST['type'], $_POST['position']);
                $stmt->execute();
                $stmt->close();

                /* Set a nice success message */
                $_SESSION['success'][] = $this->language->global->success_message->basic;
                redirect('admin/pages');
            }

        }

        /* Main View */
        $view = new \Altum\Views\View('admin/page-create/index', (array) $this);

        $this->addViewContent('content', $view->run());

    }

}
