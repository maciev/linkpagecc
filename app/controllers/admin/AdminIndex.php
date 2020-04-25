<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;

class AdminIndex extends Controller {

    public function index() {

        Authentication::guard('admin');

        $users = Database::$database->query("
            SELECT
              (SELECT COUNT(*) FROM `users` WHERE MONTH(`last_activity`) = MONTH(CURRENT_DATE()) AND YEAR(`last_activity`) = YEAR(CURRENT_DATE())) AS `active_users_month`,
              (SELECT COUNT(*) FROM `users`) AS `active_users`
        ")->fetch_object();

        $links = Database::$database->query("
            SELECT
              (SELECT COUNT(*) FROM `track_links` WHERE MONTH(`date`) = MONTH(CURRENT_DATE()) AND YEAR(`date`) = YEAR(CURRENT_DATE())) AS `clicks_month`,
              (SELECT SUM(`clicks`) FROM `links`) AS `clicks`
        ")->fetch_object();

        if($this->settings->payment->is_enabled) {
            $payments = Database::$database->query("SELECT COUNT(*) AS `payments`, IFNULL(TRUNCATE(SUM(`amount`), 2), 0) AS `earnings` FROM `payments` WHERE `currency` = '{$this->settings->payment->currency}'")->fetch_object();

            /* Data for the months transactions and earnings */
            $payments_month_result = Database::$database->query("SELECT COUNT(*) AS `payments`, DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`, TRUNCATE(SUM(`amount`), 2) AS `earnings` FROM `payments` WHERE MONTH(`date`) = MONTH(CURRENT_DATE()) AND YEAR(`date`) = YEAR(CURRENT_DATE()) GROUP BY `formatted_date`");
            $payments_month_chart = [];
            $payments_month = 0;
            $earnings_month = 0;

            /* Iterating and storing proper data for charts and later use */
            while ($row = $payments_month_result->fetch_object()) {

                $payments_month_chart[$row->formatted_date] = [
                    'payments' => $row->payments,
                    'earnings' => $row->earnings
                ];

                $payments_month += $row->payments;
                $earnings_month += $row->earnings;

            }

            /* Defining the chart data */
            $payments_month_chart = get_chart_data($payments_month_chart);
        } else {
            $payments = $payments_month = $earnings_month = $payments_month_chart = null;
        }

        /* Main View */
        $data = [
            'payments' => $payments,
            'links' => $links,
            'users' => $users,
            'payments_month' => $payments_month,
            'earnings_month' => $earnings_month,
            'payments_month_chart' => $payments_month_chart
        ];

        $view = new \Altum\Views\View('admin/index/index', (array) $this);

        $this->addViewContent('content', $view->run($data));

    }

}
