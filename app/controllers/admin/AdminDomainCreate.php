<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Logger;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;

class AdminDomainCreate extends Controller {

    public function index() {

        Authentication::guard('admin');

        /* Default variables */
        $values = [
            'scheme' => '',
            'host' => '',
        ];

        if(!empty($_POST)) {

            /* Clean some posted variables */
            $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? Database::clean_string($_POST['scheme']) : 'https://';
            $_POST['host'] = Database::clean_string($_POST['host']);

            /* Default variables */
            $values['scheme'] = $_POST['scheme'];
            $values['host'] = $_POST['host'];

            /* Must have fields */
            $fields = ['scheme', 'host'];

            /* Check for any errors */
            foreach($_POST as $key=>$value) {
                if(empty($value) && in_array($key, $fields) == true) {
                    $_SESSION['error'][] = $this->language->global->error_message->empty_fields;
                    break 1;
                }
            }

            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            /* If there are no errors continue the registering process */
            if(empty($_SESSION['error'])) {
                /* Define some needed variables */
                $type = 1;

                /* Add the row to the database */
                $stmt = Database::$database->prepare("INSERT INTO `domains` (`user_id`, `scheme`, `host`, `type`, `date`) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('sssss', $this->user->user_id, $_POST['scheme'], $_POST['host'], $type, \Altum\Date::$date);
                $stmt->execute();
                $stmt->close();

                /* Success message */
                $_SESSION['success'][] = $this->language->admin_domain_create->success_message->created;

                /* Redirect */
                redirect('admin/domains');
            }

        }

        /* Main View */
        $data = ['values' => $values];

        $view = new \Altum\Views\View('admin/domain-create/index', (array) $this);

        $this->addViewContent('content', $view->run($data));

    }

}
