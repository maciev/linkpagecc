<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Title;

class Page extends Controller {

    public function index() {

        $url = isset($this->params[0]) ? Database::clean_string($this->params[0]) : false;

        /* If the custom page url is set then try to get data from the database */
        $page = $url ? Database::get('*', 'pages', ['url' => $url]) : false;

        /* Redirect if the page does not exist */
        if(!$page) {
            $_SESSION['info'][] = $this->language->page->info_message->invalid_page;
            redirect();
        }

        /* Prepare the View */
        $data = [
            'page'  => $page
        ];

        $view = new \Altum\Views\View('page/index', (array) $this);

        $this->addViewContent('content', $view->run($data));

        /* Set a custom title */
        Title::set($page->title);

    }

}
