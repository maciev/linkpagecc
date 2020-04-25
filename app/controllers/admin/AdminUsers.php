<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Models\Package;
use Altum\Models\User;
use Altum\Middlewares\Authentication;
use Altum\Response;
use Altum\Routing\Router;

class AdminUsers extends Controller {

    public function index() {

        Authentication::guard('admin');

        /* Main View */
        $view = new \Altum\Views\View('admin/users/index', (array) $this);

        $this->addViewContent('content', $view->run());

    }


    public function get() {

        Authentication::guard('admin');

        $datatable = new \Altum\DataTable();
        $datatable->set_accepted_columns(['user_id', 'name', 'email', 'date', 'type', 'active']);
        $datatable->process($_POST);

        $result = Database::$database->query("
            SELECT
                `user_id`, `name`, `email`, `date`, `type`, `active`, `package_id`,
                (SELECT COUNT(*) FROM `users`) AS `total_before_filter`,
                (SELECT COUNT(*) FROM `users` WHERE `name` LIKE '%{$datatable->get_search()}%' OR `email` LIKE '%{$datatable->get_search()}%') AS `total_after_filter`
            FROM
                `users`
            WHERE
                `name` LIKE '%{$datatable->get_search()}%'
                OR `email` LIKE '%{$datatable->get_search()}%'
            ORDER BY
                `type` DESC,
                " . $datatable->get_order() . "
            LIMIT
                {$datatable->get_start()}, {$datatable->get_length()}
        ");

        $total_before_filter = 0;
        $total_after_filter = 0;

        $data = [];

        while($row = $result->fetch_object()):

            $email_extra = $row->type > 0 ? ' <span class="text-muted" data-toggle="tooltip" title="' . $this->language->admin_users->tooltip->admin .'"><i class="fa fa-bookmark fa-sm"></i></span>' : '';
            $row->email = $row->email . $email_extra;

            /* Active Status badge */
            $row->active = $row->active ? '<span class="badge badge-pill badge-success"><i class="fa fa-check"></i> ' . $this->language->global->active . '</span>' : '<span class="badge badge-pill badge-warning"><i class="fa fa-eye-slash"></i> ' . $this->language->global->disabled . '</span>';

            /* Current Package */
            $package = (new Package(['settings' => $this->settings]))->get_package_by_id($row->package_id);

            $row->package_id =  $package ? '<span class="badge badge-pill badge-light" data-toggle="tooltip" title="' . $this->language->admin_users->tooltip->package . '">' . $package->name . '</span>' : null;

            $row->date = '<span data-toggle="tooltip" title="' . \Altum\Date::get($row->date, true) . '">' . \Altum\Date::get($row->date, 2) . '</span>';
            $row->actions = get_admin_options_button('user', $row->user_id);

            $data[] = $row;
            $total_before_filter = $row->total_before_filter;
            $total_after_filter = $row->total_after_filter;

        endwhile;

        Response::simple_json([
            'data' => $data,
            'draw' => $datatable->get_draw(),
            'recordsTotal' => $total_before_filter,
            'recordsFiltered' =>  $total_after_filter
        ]);

    }

    public function delete() {

        Authentication::guard();

        $user_id = (isset($this->params[0])) ? $this->params[0] : false;

        if(!Csrf::check()) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
        }

        if($user_id == $this->user->user_id) {
            $_SESSION['error'][] = $this->language->admin_users->error_message->self_delete;
        }

        if(empty($_SESSION['error'])) {

            /* Delete the user */
            (new User(['settings' => $this->settings]))->delete($user_id);
            redirect('admin/users');

        }

        die();
    }

}
