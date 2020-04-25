<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Models\User;
use Altum\Title;
use MaxMind\Db\Reader;
use Sinergi\BrowserDetector\Browser;
use Sinergi\BrowserDetector\Os;

class Link extends Controller {
    public $link;

    public function index() {

        $link_url = isset($this->params[0]) ? Database::clean_string($this->params[0]) : false;

        /* Check if the current link accessed is actually the original url or not ( multi domain use ) */
        $original_url_host = parse_url(url())['host'];
        $request_url_host = Database::clean_string($_SERVER['HTTP_HOST']);

        if($original_url_host == $request_url_host) {
            $this->link = Database::get('*', 'links', ['url' => $link_url, 'is_enabled' => 1]);
        } else {
            $this->link = $this->database->query("
                        SELECT `links`.*, `domains`.`host` 
                        FROM `links`
                        LEFT JOIN `domains` ON `links`.`domain_id` = `domains`.`domain_id`
                        WHERE
                            `links`.`url` = '{$link_url}' AND 
                            `links`.`is_enabled` = 1 AND 
                            `domains`.`host` = '{$request_url_host}' AND 
                            (`links`.`user_id` = `domains`.`user_id` OR `domains`.`type` = 1)
                    ")->fetch_object() ?? null;
        }

        if(!$this->link) {
            redirect();
        }

        $user = (new User())->get($this->link->user_id);

        /* Check if its a scheduled link and we should show it or not */
        if($user->package_settings->scheduling && !empty($this->link->start_date) && !empty($this->link->end_date) && (new \DateTime() < new \DateTime($this->link->start_date) || new \DateTime() > new \DateTime($this->link->end_date))) {
            redirect();
        }

        /* Parse the settings */
        $this->link->settings = json_decode($this->link->settings);

        /* Only parse and add statistics if its not coming from inside the preview iframe from the settings */
        if(!isset($_GET['preview'])) {
            /* Generate an id for the log */
            $dynamic_id = md5(
                $this->link->link_id . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . (new \DateTime())->format('Y-m-d')
            );

            /* Detect the location */
            $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-Country.mmdb'))->get(get_ip());
            $location = $maxmind ? $maxmind['country']['iso_code'] : null;

            /* Detect extra details about the user */
            $os = (new Os($_SERVER['HTTP_USER_AGENT']))->getName();
            $browser = (new Browser($_SERVER['HTTP_USER_AGENT']))->getName();
            $referer = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

            /* Insert or update the log */
            $is_insert = true;
            $stmt = Database::$database->prepare("
                INSERT INTO 
                    `track_links` (`link_id`, `dynamic_id`, `ip`, `location`, `os`, `browser`, `referer`, `date`, `last_date`) 
                VALUES 
                    (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    `count` = `count` + 1,
                    `last_date` = VALUES (last_date)  
            ");
            $stmt->bind_param(
                'sssssssss',
                $this->link->link_id,
                $dynamic_id,
                $_SERVER['REMOTE_ADDR'],
                $location,
                $os,
                $browser,
                $referer,
                Date::$date,
                Date::$date
            );
            $stmt->execute();
            if($stmt->affected_rows > 1) {
                $is_insert = false;
            }
            $stmt->close();

            /* Add the unique hit to the link table as well */
            if ($is_insert) {
                Database::$database->query("UPDATE `links` SET `clicks` = `clicks` + 1 WHERE `link_id` = {$this->link->link_id}");
            }
        }


        /* Check what to do next */
        if($this->link->type == 'biolink' && $this->link->subtype == 'base') {

            $this->process_biolink();

        } else {

            $this->process_redirect();

        }

    }

    public function process_biolink() {

        /* Get all the links inside of the biolink */
        $links_result = Database::$database->query("SELECT * FROM `links` WHERE `biolink_id` = {$this->link->link_id} AND `type` = 'biolink' AND `subtype` <> 'base' AND `is_enabled` = 1 ORDER BY `order` ASC");

        /* Prepare the View */
        $data = [
            'link' => $this->link,
            'links_result' => $links_result
        ];

        /* Get the details of the user that owns the link */
        $user = (new User())->get($this->link->user_id);

        $view_content = \Altum\Link::get_biolink($this->link, $user, $links_result)->html;

        $this->addViewContent('content', $view_content);

        /* Set a custom title */
        Title::set($this->link->settings->title, true);
    }

    public function process_redirect() {

        /* Check if we should redirect the user or kill the script */
        if(isset($_GET['no_redirect'])) {
            die();
        }

        header('Location: ' . $this->link->location_url);

    }
}
