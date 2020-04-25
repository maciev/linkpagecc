<?php

namespace Altum\Controllers;


use Altum\Database\Database;

class Index extends Controller {

    public function index() {

        /* Custom index redirect if set */
        if(!empty($this->settings->index_url)) {
            header('Location: ' . $this->settings->index_url);
            die();
        }

        /* Check if the current link accessed is actually the original url or not ( multi domain use ) */
        $original_url_host = parse_url(url())['host'];
        $request_url_host = Database::clean_string($_SERVER['HTTP_HOST']);

        if($original_url_host != $request_url_host) {
            die('Ready to use as a custom domain.');
        }

        /* Packages View */
        $data = [
            'simple_package_settings' => [
                'no_ads',
                'removable_branding',
                'custom_branding',
                'custom_colored_links',
                'statistics',
                'google_analytics',
                'facebook_pixel',
                'custom_backgrounds',
                'verified',
                'scheduling'
            ]
        ];

        $view = new \Altum\Views\View('partials/packages', (array) $this);

        $this->addViewContent('packages', $view->run($data));


        /* Main View */
        $data = [];

        $view = new \Altum\Views\View('index/index', (array) $this);

        $this->addViewContent('content', $view->run($data));

    }

}
