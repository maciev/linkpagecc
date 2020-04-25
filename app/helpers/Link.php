<?php

namespace Altum;

class Link {

    public static function get_biolink($link, $user = null, $links_result = null) {

        /* Determine the background of the biolink */
        $link->design = new \StdClass();
        $link->design->background_class = '';
        $link->design->background_style = '';

        /* Check if the user has the access needed from the package */
        if(!$user->package_settings->custom_backgrounds && in_array($link->settings->background_type, ['image', 'gradient', 'color'])) {

            /* Revert to a default if no access */
            $link->settings->background_type = 'preset';
            $link->settings->background = 'one';

        }

        switch($link->settings->background_type) {
            case 'image':

                $link->design->background_style = 'background: url(\'' . url(UPLOADS_URL_PATH . 'backgrounds/' . $link->settings->background ) . '\');';

                break;

            case 'gradient':

                $link->design->background_style = 'background-image: linear-gradient(135deg, ' . $link->settings->background->color_one . ' 10%, ' . $link->settings->background->color_two . ' 100%);';

                break;

            case 'color':

                $link->design->background_style = 'background: ' . $link->settings->background . ';';

                break;

            case 'preset':

                $link->design->background_class = 'link-body-background-' . $link->settings->background;

                break;
        }

        /* Determine the color of the header text */
        $link->design->text_style = 'color: ' . $link->settings->text_color;


        /* Determine the notification branding settings */
        if($user && !$user->package_settings->removable_branding && !$link->settings->display_branding) {
            $link->settings->display_branding = true;
        }

        if($user && $user->package_settings->removable_branding && !$link->settings->display_branding) {
            $link->settings->display_branding = false;
        }

        /* Check if we can show the custom branding if available */
        if(isset($link->settings->branding, $link->settings->branding->name, $link->settings->branding->url) && !$user->package_settings->custom_branding) {
            $link->settings->branding = false;
        }

        $data = require THEME_PATH . 'views/link-path/partials/biolink.php';

        return $data;

    }

    public static function get_biolink_link($link, $user = null) {

        $data = null;

        /* Require different files for different types of links available */
        switch($link->subtype) {
            case 'link':

                $link->settings = json_decode($link->settings);

                /* Check if the user has the access needed from the package */
                if(!$user->package_settings->custom_colored_links) {

                    /* Revert to a default if no access */
                    $link->settings->background_color = 'white';
                    $link->settings->text_color = 'black';

                    if($link->settings->outline) {
                        $link->settings->background_color = 'white';
                        $link->settings->text_color = 'white';
                    }
                }

                /* Determine the css and styling of the button */
                $link->design = new \StdClass();
                $link->design->link_class = '';
                $link->design->link_style = 'background: ' . $link->settings->background_color . ';color: ' . $link->settings->text_color;

                /* Type of button */
                if($link->settings->outline) {
                    $link->design->link_style = 'color: ' . $link->settings->text_color . '; background: transparent; border: .1rem solid ' . $link->settings->background_color;
                }

                /* Border radius */
                switch($link->settings->border_radius) {
                    case 'straight':
                        break;

                    case 'round':
                        $link->design->link_class = 'link-btn-round';
                        break;

                    case 'rounded':
                        $link->design->link_class = 'link-btn-rounded';
                        break;
                }

                /* Animation */
                if($link->settings->animation) {
                    $link->design->link_class .= ' animated infinite ' . $link->settings->animation . ' delay-2s';
                }

                $data = require THEME_PATH . 'views/link-path/partials/biolink_link.php';

                break;

            case 'youtube':

                if(preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})(?:&list=(\S+))?$/', $link->location_url, $match)) {
                    $embed = $match[1];

                    $data = require THEME_PATH . 'views/link-path/partials/biolink_link_youtube.php';
                }

                break;

            case 'soundcloud':

                if(preg_match('/(soundcloud\.com)/', $link->location_url)) {
                    $embed = $link->location_url;

                    $data = require THEME_PATH . 'views/link-path/partials/biolink_link_soundcloud.php';
                }

                break;

            case 'vimeo':

                if(preg_match('/https:\/\/(player\.)?vimeo\.com(\/video)?\/(\d+)/', $link->location_url, $match)) {
                    $embed = $match[3];

                    $data = require THEME_PATH . 'views/link-path/partials/biolink_link_vimeo.php';
                }

                break;

            case 'twitch':

                if(preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:twitch\.tv\/)(.+)$/', $link->location_url, $match)) {
                    $embed = $match[1];

                    $data = require THEME_PATH . 'views/link-path/partials/biolink_link_twitch.php';
                }

                break;

            case 'spotify':

                if(preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:open\.)?(?:spotify\.com\/)(album|track|show|episode)+\/(.+)$/', $link->location_url, $match)) {
                    $embed_type = $match[1];
                    $embed_value = $match[2];

                    $data = require THEME_PATH . 'views/link-path/partials/biolink_link_spotify.php';
                }

                break;
        }


        return $data;

    }
}
