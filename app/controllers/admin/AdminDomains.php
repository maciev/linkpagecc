<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;
use Altum\Response;

class AdminDomains extends Controller {

    public function index() {

        Authentication::guard('admin');

        /* Main View */
        $view = new \Altum\Views\View('admin/domains/index', (array) $this);

        $this->addViewContent('content', $view->run());

    }


    public function get() {

        Authentication::guard('admin');

        $datatable = new \Altum\DataTable();
        $datatable->set_accepted_columns(['domain_id', 'type', 'host', 'date']);
        $datatable->process($_POST);

        $result = Database::$database->query("
            SELECT
                `domains`.*,
                COUNT(`links`.`domain_id`) AS `links`,
                (SELECT COUNT(*) FROM `domains`) AS `total_before_filter`,
                (SELECT COUNT(*) FROM `domains` WHERE `domains`.`host` LIKE '%{$datatable->get_search()}%') AS `total_after_filter`
            FROM
                `domains`
            LEFT JOIN
                `links` ON `domains`.`domain_id` = `links`.`domain_id`
            WHERE 
                `domains`.`host` LIKE '%{$datatable->get_search()}%'
            GROUP BY
                `domain_id`
            ORDER BY
                `domain_id` ASC,
                " . $datatable->get_order() . "
            LIMIT
                {$datatable->get_start()}, {$datatable->get_length()}
        ");

        $total_before_filter = 0;
        $total_after_filter = 0;

        $data = [];

        while($row = $result->fetch_object()):

            /* Type */
            $row->type = $row->type == 1 ? '<span class="badge badge-pill badge-success"><i class="fa fa-globe"></i> ' . $this->language->admin_domains->display->type_global . '</span>' : null;

            /* host */
            $host_prepend = '<span class="badge badge-pill badge-info">' . $row->scheme . '</span> ';
            $row->host = $host_prepend . $row->host;

            /* Links */
            $row->links = '<i class="fa fa-link text-muted"></i> ' . nr($row->links);

            $row->date = '<span data-toggle="tooltip" title="' . \Altum\Date::get($row->date, true) . '">' . \Altum\Date::get($row->date, 2) . '</span>';
            $row->actions = get_admin_options_button('domain', $row->domain_id);

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

        $domain_id = (isset($this->params[0])) ? (int) $this->params[0] : false;

        if(!Csrf::check()) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
        }

        if(empty($_SESSION['error'])) {

            /* Delete the domain */
            $this->database->query("DELETE FROM `domains` WHERE `domain_id` = {$domain_id}");

            /* Delete all the links using that domain */
            $this->database->query("DELETE FROM `links` WHERE `domain_id` = {$domain_id}");

            redirect('admin/domains');

        }

        die();
    }

}
