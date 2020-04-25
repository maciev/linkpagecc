<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Response;
use Altum\Routing\Router;

class LinkAjax extends Controller {

    public function index() {

        Authentication::guard();

        if(!empty($_POST) && (Csrf::check('token') || Csrf::check('global_token')) && isset($_POST['request_type'])) {

            switch($_POST['request_type']) {

                /* Status toggle */
                case 'is_enabled_toggle': $this->is_enabled_toggle(); break;

                /* Order links */
                case 'order': $this->order(); break;

                /* Create */
                case 'create': $this->create(); break;

                /* Update */
                case 'update': $this->update(); break;

                /* Delete */
                case 'delete': $this->delete(); break;

            }

        }

        die();
    }

    private function is_enabled_toggle() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        /* Get the current status */
        $is_enabled = Database::simple_get('is_enabled', 'links', ['link_id' => $_POST['link_id']]);

        if($is_enabled !== false) {
            $new_is_enabled = (int) !$is_enabled;

            Database::$database->query("UPDATE `links` SET `is_enabled` = {$new_is_enabled} WHERE `user_id` = {$this->user->user_id} AND `link_id` = {$_POST['link_id']}");

            Response::json('', 'success');
        }
    }

    private function order() {

        if(isset($_POST['links']) && is_array($_POST['links'])) {
            foreach($_POST['links'] as $link) {
                $link['link_id'] = (int) $link['link_id'];
                $link['order'] = (int) $link['order'];

                /* Update the link order */
                $stmt = $this->database->prepare("UPDATE `links` SET `order` = ? WHERE `link_id` = ? AND `user_id` = ?");
                $stmt->bind_param('sss', $link['order'], $link['link_id'], $this->user->user_id);
                $stmt->execute();
                $stmt->close();

            }
        }

        Response::json('', 'success');
    }

    private function create() {
        $_POST['type'] = trim(Database::clean_string($_POST['type']));

        /* Check for possible errors */
        if(!in_array($_POST['type'], ['link', 'biolink'])) {
            die();
        }

        switch($_POST['type']) {
            case 'link':

                $this->create_link();

                break;

            case 'biolink':

                $biolink_link_types = require APP_PATH . 'includes/biolink_link_types.php';

                /* Check for subtype */
                if(isset($_POST['subtype']) && in_array($_POST['subtype'], $biolink_link_types)) {
                    $_POST['subtype'] = trim(Database::clean_string($_POST['subtype']));

                    if($_POST['subtype'] == 'link') {
                        $this->create_biolink_link();
                    } else {
                        $this->create_biolink_other($_POST['subtype']);
                    }


                } else {
                    /* Base biolink */
                    $this->create_biolink();
                }

                break;
        }

        die();
    }

    private function create_link() {
        $_POST['project_id'] = (int) $_POST['project_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));
        $_POST['url'] = !empty($_POST['url']) ? get_slug(Database::clean_string($_POST['url'])) : false;

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id']);

        if(!Database::exists('project_id', 'projects', ['user_id' => $this->user->user_id, 'project_id' => $_POST['project_id']])) {
            die();
        }

        if(empty($_POST['location_url'])) {
            Response::json($this->language->global->error_message->empty_fields, 'error');
        }

        $this->check_url($_POST['url']);

        $this->check_location_url($_POST['location_url']);

        /* Make sure that the user didn't exceed the limit */
        $user_total_links = Database::$database->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'link'")->fetch_object()->total;
        if($this->user->package_settings->links_limit != -1 && $user_total_links >= $this->user->package_settings->links_limit) {
            Response::json($this->language->create_link_modal->error_message->links_limit, 'error');
        }

        /* Check for duplicate url if needed */
        if($_POST['url']) {

            if(Database::exists('link_id', 'links', ['url' => $_POST['url'], 'domain_id' => $domain_id])) {
                Response::json($this->language->create_link_modal->error_message->url_exists, 'error');
            }

        }

        if(empty($errors)) {
            $url = $_POST['url'] ? $_POST['url'] : string_generate(10);
            $type = 'link';
            $subtype = '';
            $settings = '';

            /* Generate random url if not specified */
            while(Database::exists('link_id', 'links', ['url' => $url, 'domain_id' => $domain_id])) {
                $url = string_generate(10);
            }

            /* Insert to database */
            $stmt = Database::$database->prepare("INSERT INTO `links` (`project_id`, `user_id`, `domain_id`, `type`, `subtype`, `url`, `location_url`, `settings`, `date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssssssss', $_POST['project_id'], $this->user->user_id, $domain_id, $type, $subtype, $url, $_POST['location_url'], $settings, \Altum\Date::$date);
            $stmt->execute();
            $link_id = $stmt->insert_id;
            $stmt->close();

            Response::json('', 'success', ['url' => url('link/' . $link_id)]);
        }
    }

    private function create_biolink() {
        $_POST['project_id'] = (int) $_POST['project_id'];
        $_POST['url'] = !empty($_POST['url']) ? get_slug(Database::clean_string($_POST['url'])) : false;

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id']);

        if(!Database::exists('project_id', 'projects', ['user_id' => $this->user->user_id, 'project_id' => $_POST['project_id']])) {
            die();
        }

        /* Make sure that the user didn't exceed the limit */
        $user_total_biolinks = Database::$database->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'biolink' AND `subtype` = 'base'")->fetch_object()->total;
        if($this->user->package_settings->biolinks_limit != -1 && $user_total_biolinks >= $this->user->package_settings->biolinks_limit) {
            Response::json($this->language->create_biolink_modal->error_message->biolinks_limit, 'error');
        }

        /* Check for duplicate url if needed */
        if($_POST['url']) {
            if(Database::exists('link_id', 'links', ['url' => $_POST['url'], 'domain_id' => $domain_id])) {
                Response::json($this->language->create_biolink_modal->error_message->url_exists, 'error');
            }
        }

        /* Start the creation process */
        $url = $_POST['url'] ? $_POST['url'] : string_generate(10);
        $type = 'biolink';
        $subtype = 'base';
        $settings = json_encode([
            'title' => $this->language->link->biolink->title_default,
            'description' => $this->language->link->biolink->description_default,
            'image' => '',
            'background_type' => 'preset',
            'background' => 'one',
            'text_color' => 'white',
            'google_analytics' => '',
            'facebook_pixel' => '',
            'display_branding' => true,
            'branding' => [
                'url' => '',
                'name' => ''
            ]
        ]);

        /* Generate random url if not specified */
        while(Database::exists('link_id', 'links', ['url' => $url, 'domain_id' => $domain_id])) {
            $url = string_generate(10);
        }

        /* Insert to database */
        $stmt = Database::$database->prepare("INSERT INTO `links` (`project_id`, `user_id`, `domain_id`, `type`, `subtype`, `url`, `settings`, `date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssss', $_POST['project_id'], $this->user->user_id, $domain_id, $type, $subtype, $url,  $settings, \Altum\Date::$date);
        $stmt->execute();
        $link_id = $stmt->insert_id;
        $stmt->close();

        /* Insert a first biolink link */
        $url = string_generate(10);
        $location_url = url();
        $type = 'biolink';
        $subtype = 'link';
        $settings = json_encode([
            'name' => $this->language->link->biolink->link->name_default,
            'text_color' => 'black',
            'background_color' => 'white',
            'outline' => false,
            'border_radius' => 'rounded',
            'animation' => false,
            'icon' => ''
        ]);

        /* Generate random url if not specified */
        while(Database::exists('link_id', 'links', ['url' => $url])) {
            $url = string_generate(10);
        }

        $stmt = Database::$database->prepare("INSERT INTO `links` (`project_id`, `biolink_id`, `user_id`, `type`, `subtype`, `url`, `location_url`, `settings`, `date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssssss', $_POST['project_id'], $link_id, $this->user->user_id, $type, $subtype, $url, $location_url, $settings, \Altum\Date::$date);
        $stmt->execute();
        $stmt->close();

        Response::json('', 'success', ['url' => url('link/' . $link_id)]);
    }

    private function create_biolink_link() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));

        $this->check_location_url($_POST['location_url']);

        if(!$project_id = Database::simple_get('project_id', 'links', ['user_id' => $this->user->user_id, 'link_id' => $_POST['link_id'], 'type' => 'biolink', 'subtype' => 'base'])) {
            die();
        }

        $url = string_generate(10);
        $type = 'biolink';
        $subtype = 'link';
        $settings = json_encode([
            'name' => $this->language->link->biolink->link->name_default,
            'text_color' => 'black',
            'background_color' => 'white',
            'outline' => false,
            'border_radius' => 'rounded',
            'animation' => false,
            'icon' => ''
        ]);

        /* Generate random url if not specified */
        while(Database::exists('link_id', 'links', ['url' => $url])) {
            $url = string_generate(10);
        }

        $stmt = Database::$database->prepare("INSERT INTO `links` (`project_id`, `biolink_id`, `user_id`, `type`, `subtype`, `url`, `location_url`, `settings`, `date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssssss', $project_id, $_POST['link_id'], $this->user->user_id, $type, $subtype, $url, $_POST['location_url'], $settings, \Altum\Date::$date);
        $stmt->execute();
        $stmt->close();

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_other($subtype) {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));

        $this->check_location_url($_POST['location_url']);

        if(!$project_id = Database::simple_get('project_id', 'links', ['user_id' => $this->user->user_id, 'link_id' => $_POST['link_id'], 'type' => 'biolink', 'subtype' => 'base'])) {
            die();
        }

        $url = string_generate(10);
        $type = 'biolink';
        $settings = json_encode([]);

        /* Generate random url if not specified */
        while(Database::exists('link_id', 'links', ['url' => $url])) {
            $url = string_generate(10);
        }

        $stmt = Database::$database->prepare("INSERT INTO `links` (`project_id`, `biolink_id`, `user_id`, `type`, `subtype`, `url`, `location_url`, `settings`, `date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssssss', $project_id, $_POST['link_id'], $this->user->user_id, $type, $subtype, $url, $_POST['location_url'], $settings, \Altum\Date::$date);
        $stmt->execute();
        $stmt->close();

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update() {

        if(!empty($_POST)) {
            $_POST['type'] = trim(Database::clean_string($_POST['type']));

            /* Check for possible errors */
            if(!in_array($_POST['type'], ['link', 'biolink'])) {
                die();
            }
            if(!Csrf::check()) {
                Response::json($this->language->global->error_message->invalid_csrf_token, 'error');
            }

            switch($_POST['type']) {
                case 'link':

                    $this->update_link();

                    break;

                case 'biolink':

                    $biolink_link_types = require APP_PATH . 'includes/biolink_link_types.php';

                    /* Check for subtype */
                    if(isset($_POST['subtype']) && in_array($_POST['subtype'], $biolink_link_types)) {
                        $_POST['subtype'] = trim(Database::clean_string($_POST['subtype']));

                        if($_POST['subtype'] == 'link') {
                            $this->update_biolink_link();
                        } else {
                            $this->update_biolink_other($_POST['subtype']);
                        }


                    } else {
                        /* Base biolink */
                        $this->update_biolink();
                    }

                    break;
            }

        }

        die();
    }

    private function update_biolink() {
        $image_allowed_extensions = ['jpg', 'jpeg', 'png', 'svg', 'ico'];
        $image = (bool) !empty($_FILES['image']['name']);
        $_POST['title'] = Database::clean_string($_POST['title']);
        $_POST['description'] = Database::clean_string($_POST['description']);
        $_POST['url'] = !empty($_POST['url']) ? get_slug(Database::clean_string($_POST['url'])) : false;

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id']);

        /* Check for any errors */
        if(!$link = Database::get('*', 'links', ['link_id' => $_POST['link_id'], 'user_id' => $this->user->user_id])) {
            die();
        }

        $link->settings = json_decode($link->settings);

        /* Check for any errors on the logo image */
        if($image) {
            $image_file_extension = explode('.', $_FILES['image']['name']);
            $image_file_extension = strtolower(end($image_file_extension));
            $image_file_temp = $_FILES['image']['tmp_name'];

            if($_FILES['image']['error']) {
                Response::json($this->language->global->error_message->file_upload, 'error');
            }

            if(!in_array($image_file_extension, $image_allowed_extensions)) {
                Response::json($this->language->global->error_message->invalid_file_type, 'error');
            }
        }

        if($_POST['url'] == $link->url) {
            $url = $link->url;
        } else {
            $url = $_POST['url'] ? $_POST['url'] : string_generate(10);

            /* Generate random url if not specified */
            while(Database::exists('link_id', 'links', ['url' => $url, 'domain_id' => $domain_id])) {
                $url = string_generate(10);
            }
        }

        /* Update the avatar of the profile if needed */
        if($image) {

            /* Delete current image */
            if(!empty($link->settings->image) && file_exists(UPLOADS_PATH . 'avatars/' . $link->settings->image)) {
                unlink(UPLOADS_PATH . 'avatars/' . $link->settings->image);
            }

            /* Generate new name for logo */
            $image_new_name = md5(time() . rand()) . '.' . $image_file_extension;

            /* Upload the original */
            move_uploaded_file($image_file_temp, UPLOADS_PATH . 'avatars/' . $image_new_name);

        }

        $_POST['text_color'] = !preg_match('/#([a-f0-9]{3}){1,2}\b/i', $_POST['text_color']) ? '#fff' : $_POST['text_color'];
        $biolink_backgrounds = require APP_PATH . 'includes/biolink_backgrounds.php';
        $_POST['background_type'] = array_key_exists($_POST['background_type'], $biolink_backgrounds) ? $_POST['background_type'] : 'preset';
        $background = 'one';

        switch($_POST['background_type']) {
            case 'preset':
                $background = in_array($_POST['background'], $biolink_backgrounds['preset']) ? $_POST['background'] : 'one';
                break;

            case 'color':

                $background = !preg_match('/#([a-f0-9]{3}){1,2}\b/i', $_POST['background']) ? '#000' : $_POST['background'];

                break;

            case 'gradient':

                $color_one = !preg_match('/#([a-f0-9]{3}){1,2}\b/i', $_POST['background'][0]) ? '#000' : $_POST['background'][0];
                $color_two = !preg_match('/#([a-f0-9]{3}){1,2}\b/i', $_POST['background'][1]) ? '#000' : $_POST['background'][1];

                $background = [
                    'color_one' => $color_one,
                    'color_two' => $color_two
                ];

                break;

            case 'image':

                $background = (bool) !empty($_FILES['background']['name']);

                /* Check for any errors on the logo image */
                if($background) {
                    $background_file_extension = explode('.', $_FILES['background']['name']);
                    $background_file_extension = strtolower(end($background_file_extension));
                    $background_file_temp = $_FILES['background']['tmp_name'];

                    if($_FILES['background']['error']) {
                        Response::json($this->language->global->error_message->file_upload, 'error');
                    }

                    if(!in_array($background_file_extension, $image_allowed_extensions)) {
                        Response::json($this->language->global->error_message->invalid_file_type, 'error');
                    }

                    /* Delete current image */
                    if(!empty($link->settings->background) && file_exists(UPLOADS_PATH . 'backgrounds/' . $link->settings->background)) {
                        unlink(UPLOADS_PATH . 'backgrounds/' . $link->settings->background);
                    }

                    /* Generate new name for logo */
                    $background_new_name = md5(time() . rand()) . '.' . $background_file_extension;

                    /* Upload the original */
                    move_uploaded_file($background_file_temp, UPLOADS_PATH . 'backgrounds/' . $background_new_name);

                    $background = $background_new_name;
                }

                break;
        }

        $_POST['display_branding'] = (bool) isset($_POST['display_branding']);
        $_POST['branding_name'] = Database::clean_string($_POST['branding_name']);
        $_POST['branding_url'] = Database::clean_string($_POST['branding_url']);
        $_POST['google_analytics'] = Database::clean_string($_POST['google_analytics']);
        $_POST['facebook_pixel'] = Database::clean_string($_POST['facebook_pixel']);

        /* Set the new settings variable */
        $settings = json_encode([
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'image' => $image ? $image_new_name : $link->settings->image,
            'background_type' => $_POST['background_type'],
            'background' => $background,
            'text_color' => $_POST['text_color'],
            'google_analytics' => $_POST['google_analytics'],
            'facebook_pixel' => $_POST['facebook_pixel'],
            'display_branding' => $_POST['display_branding'],
            'branding' => [
                'name' => $_POST['branding_name'],
                'url' => $_POST['branding_url'],
            ]
        ]);

        /* Update the record */
        $stmt = Database::$database->prepare("UPDATE `links` SET `domain_id` = ?, `url` = ?, `settings` = ? WHERE `link_id` = ?");
        $stmt->bind_param('ssss', $domain_id, $url, $settings, $link->link_id);
        $stmt->execute();
        $stmt->close();

        Response::json($this->language->link->success_message->settings_updated, 'success');

    }

    private function update_biolink_link() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));
        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $_POST['url'] = !empty($_POST['url']) ? get_slug(Database::clean_string($_POST['url'])) : false;
        $_POST['outline'] = (bool) isset($_POST['outline']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? Database::clean_string($_POST['border_radius']) : 'rounded';
        $_POST['animation'] = in_array($_POST['animation'], ['false', 'bounce', 'wobble', 'wobble', 'swing', 'shake', 'rubberBand', 'pulse', 'flash']) ? Database::clean_string($_POST['animation']) : false;
        $_POST['icon'] = trim(Database::clean_string($_POST['icon']));
        $_POST['text_color'] = !preg_match('/#([a-f0-9]{3}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([a-f0-9]{3}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date']))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date']))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }

        /* Check for any errors */
        $fields = ['location_url', 'name'];

        /* Check for any errors */
        foreach($_POST as $key => $value) {
            if(empty($value) && in_array($key, $fields) == true) {
                Response::json($this->language->global->error_message->empty_fields, 'error');
                break 1;
            }
        }

        $this->check_url($_POST['url']);

        $this->check_location_url($_POST['location_url']);

        if(!$link = Database::get('*', 'links', ['link_id' => $_POST['link_id'], 'user_id' => $this->user->user_id])) {
            die();
        }

        /* Check for duplicate url if needed */
        if($_POST['url'] && $_POST['url'] != $link->url) {
            if(Database::exists('link_id', 'links', ['url' => $_POST['url']])) {
                Response::json($this->language->create_biolink_link_modal->error_message->url_exists, 'error');
            }
        } else if(!$_POST['url']) {
            $_POST['url'] = string_generate(10);

            /* Generate random url if not specified */
            while(Database::exists('link_id', 'links', ['url' => $_POST['url']])) {
                $_POST['url'] = string_generate(10);
            }
        }

        $settings = json_encode([
            'name' => $_POST['name'],
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'outline' => $_POST['outline'],
            'border_radius' => $_POST['border_radius'],
            'animation' => $_POST['animation'],
            'icon' => $_POST['icon']
        ]);

        $stmt = Database::$database->prepare("UPDATE `links` SET `url` = ?, `location_url` = ?, `settings` = ?, `start_date` = ?, `end_date` = ? WHERE `link_id` = ?");
        $stmt->bind_param('ssssss', $_POST['url'], $_POST['location_url'], $settings, $_POST['start_date'], $_POST['end_date'], $_POST['link_id']);
        $stmt->execute();
        $stmt->close();

        Response::json($this->language->link->success_message->settings_updated, 'success');
    }

    private function update_biolink_other($subtype) {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));

        $this->check_location_url($_POST['location_url']);

        if(!$link = Database::get('*', 'links', ['link_id' => $_POST['link_id'], 'user_id' => $this->user->user_id])) {
            die();
        }

        $stmt = Database::$database->prepare("UPDATE `links` SET `location_url` = ? WHERE `link_id` = ?");
        $stmt->bind_param('ss', $_POST['location_url'], $_POST['link_id']);
        $stmt->execute();
        $stmt->close();

        Response::json($this->language->link->success_message->settings_updated, 'success');
    }

    private function update_link() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['url'] = !empty($_POST['url']) ? get_slug(Database::clean_string($_POST['url'])) : false;
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date']))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date']))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id']);

        /* Check for any errors */
        $fields = ['location_url'];

        /* Check for any errors */
        foreach($_POST as $key => $value) {
            if(empty($value) && in_array($key, $fields) == true) {
                Response::json($this->language->global->error_message->empty_fields, 'error');
                break 1;
            }
        }

        $this->check_url($_POST['url']);

        $this->check_location_url($_POST['location_url']);

        if(!$link = Database::get('*', 'links', ['link_id' => $_POST['link_id'], 'user_id' => $this->user->user_id])) {
            die();
        }

        if($_POST['url'] == $link->url) {
            $url = $link->url;
        } else {
            $url = $_POST['url'] ? $_POST['url'] : string_generate(10);

            /* Generate random url if not specified */
            while(Database::exists('link_id', 'links', ['url' => $url, 'domain_id' => $domain_id])) {
                $url = string_generate(10);
            }
        }

        $stmt = Database::$database->prepare("UPDATE `links` SET `domain_id` = ?, `url` = ?, `location_url` = ?, `start_date` = ?, `end_date` = ? WHERE `link_id` = ?");
        $stmt->bind_param('ssssss', $domain_id, $url, $_POST['location_url'], $_POST['start_date'], $_POST['end_date'], $_POST['link_id']);
        $stmt->execute();
        $stmt->close();

        Response::json($this->language->link->success_message->settings_updated, 'success');
    }

    private function delete() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        /* Check for possible errors */
        if(!$link = Database::get(['project_id', 'biolink_id', 'type', 'subtype'], 'links', ['user_id' => $this->user->user_id, 'link_id' => $_POST['link_id']])) {
            die();
        }

        if(empty($errors)) {
            /* Delete from database */
            $stmt = Database::$database->prepare("DELETE FROM `links` WHERE `link_id` = ? OR `biolink_id` = ? AND `user_id` = ?");
            $stmt->bind_param('sss', $_POST['link_id'], $_POST['link_id'], $this->user->user_id);
            $stmt->execute();
            $stmt->close();

            /* Determine where to redirect the user */
            if($link->type == 'biolink' && $link->subtype != 'base') {
                $redirect_url = url('link/' . $link->biolink_id . '?tab=links');
            } else {
                $redirect_url = url('project/' . $link->project_id);
            }

            Response::json('', 'success', ['url' => $redirect_url]);
        }
    }

    /* Function to bundle together all the checks of a custom url */
    private function check_url($url) {

        if($url) {
            /* Make sure the url alias is not blocked by a route of the product */
            if(array_key_exists($url, Router::$routes[''])) {
                Response::json($this->language->link->error_message->blacklisted_url, 'error');
            }

            /* Make sure the custom url is not blacklisted */
            if(in_array($url, $this->settings->links->blacklisted_keywords)) {
                Response::json($this->language->link->error_message->blacklisted_keyword, 'error');
            }

        }

    }

    /* Function to bundle together all the checks of an url */
    private function check_location_url($url) {

        if(empty(trim($url))) {
            Response::json($this->language->global->error_message->empty_fields, 'error');
        }

        $url_details = parse_url($url);

        if(!isset($url_details['scheme']) || (isset($url_details['scheme']) && !in_array($url_details['scheme'], ['http', 'https']))) {
            Response::json($this->language->link->error_message->invalid_location_url, 'error');
        }

        /* Make sure the domain is not blacklisted */
        if(in_array(get_domain($url), $this->settings->links->blacklisted_domains)) {
            Response::json($this->language->link->error_message->blacklisted_domain, 'error');
        }

        /* Check the url with phishtank to make sure its not a phishing site */
        if($this->settings->links->phishtank_is_enabled) {
            if(phishtank_check($url, $this->settings->links->phishtank_api_key)) {
                Response::json($this->language->link->error_message->blacklisted_location_url, 'error');
            }
        }

        /* Check the url with google safe browsing to make sure it is a safe website */
        if($this->settings->links->google_safe_browsing_is_enabled) {
            if(google_safe_browsing_check($url, $this->settings->links->google_safe_browsing_api_key)) {
                Response::json($this->language->link->error_message->blacklisted_location_url, 'error');
            }
        }
    }

    /* Check if custom domain is set and return the proper value */
    private function get_domain_id($posted_domain_id) {

        $domain_id = 0;

        if(isset($posted_domain_id)) {
            $domain_id = (int) Database::clean_string($posted_domain_id);

            $domain_id = $this->database->query("SELECT `domain_id` FROM `domains` WHERE `domain_id` = {$domain_id} AND (`user_id` = {$this->user->user_id} OR `type` = 1)")->fetch_object()->domain_id ?? 0;
        }

        return $domain_id;
    }
}
