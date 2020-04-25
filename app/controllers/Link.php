<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;
use Altum\Models\Domain;
use Altum\Title;

class Link extends Controller {
    public $link;

    public function index() {

        Authentication::guard();

        $link_id = isset($this->params[0]) ? (int) $this->params[0] : false;
        $method = isset($this->params[1]) && in_array($this->params[1], ['settings', 'statistics']) ? $this->params[1] : 'settings';

        /* Make sure the link exists and is accessible to the user */
        if(!$this->link = Database::get('*', 'links', ['link_id' => $link_id, 'user_id' => $this->user->user_id])) {
            redirect('dashboard');
        }

        $this->link->settings = json_decode($this->link->settings);

        /* Get the current domain if needed */
        $this->link->domain = $this->link->domain_id ? (new Domain())->get_domain($this->link->domain_id) : null;

        /* Determine the actual full url */
        $this->link->full_url = $this->link->domain ? $this->link->domain->url . $this->link->url : url($this->link->url);

        /* Handle code for different parts of the page */
        switch($method) {
            case 'settings':

                if($this->link->type == 'biolink') {
                    /* Get the links available for the biolink */
                    $link_links_result = $this->database->query("SELECT * FROM `links` WHERE `biolink_id` = {$this->link->link_id} ORDER BY `order` ASC");

                    $biolink_link_types = require APP_PATH . 'includes/biolink_link_types.php';

                    /* Add the modals for creating the links inside the biolink */
                    foreach($biolink_link_types as $key) {
                        $data = ['link' => $this->link];
                        $view = new \Altum\Views\View('link/settings/create_' . $key . '_modal.settings.biolink.method', (array) $this);
                        \Altum\Event::add_content($view->run($data), 'modals');
                    }

                    if($this->link->subtype != 'base') {
                        redirect('link/' . $this->link->biolink_id);
                    }
                }

                /* Get the available domains to use */
                $domains = (new Domain())->get_domains($this->user->user_id);

                /* Prepare variables for the view */
                $data = [
                    'link'              => $this->link,
                    'method'            => $method,
                    'link_links_result' => $link_links_result ?? null,
                    'domains'           => $domains
                ];

                break;


            case 'statistics':

                $start_date = isset($this->params[2]) ? Database::clean_string($this->params[2]) : false;
                $end_date = isset($this->params[3]) ? Database::clean_string($this->params[3]) : false;

                $date = \Altum\Date::get_start_end_dates($start_date, $end_date);

                /* Get data needed for statistics from the database */
                $logs = [];
                $logs_chart = [];
                $logs_data = [
                    'location'      => [],
                    'os'            => [],
                    'browser'       => [],
                    'referer'       => []
                ];

                $logs_result = Database::$database->query("
                    SELECT
                         `location`,
                         `os`,
                         `browser`,
                         `referer`,
                         `count`,
                         DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`
                    FROM
                         `track_links`
                    WHERE
                        `link_id` = {$this->link->link_id}
                        AND (`date` BETWEEN '{$date->start_date_query}' AND '{$date->end_date_query}')
                    ORDER BY
                        `formatted_date`
                ");

                /* Generate the raw chart data and save logs for later usage */
                while($row = $logs_result->fetch_object()) {
                    $logs[] = $row;

                    /* Handle if the date key is not already set */
                    if(!array_key_exists($row->formatted_date, $logs_chart)) {
                        $logs_chart[$row->formatted_date] = [
                            'impression'        => 0,
                            'unique'            => 0,
                        ];
                    }

                    /* Distribute the data from the database row */
                    $logs_chart[$row->formatted_date]['unique']++;
                    $logs_chart[$row->formatted_date]['impression'] += $row->count;

                    if(!array_key_exists($row->location, $logs_data['location'])) {
                        $logs_data['location'][$row->location ?? 'false'] = 1;
                    } else {
                        $logs_data['location'][$row->location]++;
                    }

                    if(!array_key_exists($row->os, $logs_data['os'])) {
                        $logs_data['os'][$row->os ?? 'N/A'] = 1;
                    } else {
                        $logs_data['os'][$row->os]++;
                    }

                    if(!array_key_exists($row->browser, $logs_data['browser'])) {
                        $logs_data['browser'][$row->browser ?? 'N/A'] = 1;
                    } else {
                        $logs_data['browser'][$row->browser]++;
                    }

                    if(!array_key_exists($row->referer, $logs_data['referer'])) {
                        $logs_data['referer'][$row->referer ?? 'false'] = 1;
                    } else {
                        $logs_data['referer'][$row->referer]++;
                    }
                }

                $logs_chart = get_chart_data($logs_chart);

                arsort($logs_data['referer']);
                arsort($logs_data['browser']);
                arsort($logs_data['os']);
                arsort($logs_data['location']);

                /* Prepare variables for the view */
                $data = [
                    'link'              => $this->link,
                    'method'            => $method,
                    'date'              => $date,
                    'logs'              => $logs,
                    'logs_chart'        => $logs_chart,
                    'logs_data'         => $logs_data,
                ];

                break;
        }

        /* Prepare the method View */
        $view = new \Altum\Views\View('link/' . $method . '.method', (array) $this);
        $this->addViewContent('method', $view->run($data));

        /* Prepare the View */
        $data = [
            'link'      => $this->link,
            'method'    => $method
        ];

        $view = new \Altum\Views\View('link/index', (array) $this);

        $this->addViewContent('content', $view->run($data));

        /* Set a custom title */
        Title::set(sprintf($this->language->link->title, $this->link->url));

    }

}
