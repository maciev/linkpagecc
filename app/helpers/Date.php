<?php

namespace Altum;

class Date {
    public static $date;

    public static function validate($date, $format = 'Y-m-d') {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }

    /* Helper to easily and fast output dates to the screen */
    public static function get($date = '', $type = 0) {

        return $date == '' ? (new \DateTime())->format('Y-m-d H:i:s') : (new \DateTime($date))->format(self::get_format($type));

    }

    /* Helper to retrieve the current format of datetime to output */
    public static function get_format($type = 0) {

        switch($type) {
            case 0:
                return Language::get()->global->date->datetime_ymd_format;
                break;

            case 1:
                return Language::get()->global->date->datetime_ymd_format . ' ' . Language::get()->global->date->datetime_his_format;
                break;

            case 2:
                return Language::get()->global->date->datetime_readable_format;
                break;
        }

    }

    /* Helper to generate start_date and end_date for datepicker */
    public static function get_start_end_dates($start_date, $end_date) {

        $return = new \StdClass();

        /* Date selection for the notification logs */
        if($start_date && $end_date && self::validate($start_date, 'Y-m-d') && self::validate($end_date, 'Y-m-d')) {
            $return->start_date = $start_date;
            $return->start_date_query = (new \DateTime($start_date))->format('Y-m-d H:i:s');
            $return->end_date_query = (new \DateTime($end_date))->modify('+1 day')->format('Y-m-d H:i:s');
            $return->end_date = $end_date;
        } else {
            $return->start_date_query = (new \DateTime())->modify('-30 day')->format('Y-m-d H:i:s');
            $return->start_date = (new \DateTime())->modify('-30 day')->format('Y-m-d');
            $return->end_date_query = (new \DateTime())->modify('+1 day')->format('Y-m-d H:i:s');
            $return->end_date = (new \DateTime())->modify('+1 day')->format('Y-m-d');
        }

        $return->input_date_range = $return->start_date . ',' . $return->end_date;

        return $return;
    }

    /* Helper to have the timeago from one point to now */
    public static function get_timeago($date) {

        $estimate_time = time() - (new \DateTime($date))->getTimestamp();

        if($estimate_time < 1) {
            return Language::get()->global->date->now;
        }

        $condition = [
            12 * 30 * 24 * 60 * 60  =>  'year',
            30 * 24 * 60 * 60       =>  'month',
            24 * 60 * 60            =>  'day',
            60 * 60                 =>  'hour',
            60                      =>  'minute',
            1                       =>  'second'
        ];

        foreach($condition as $secs => $str) {
            $d = $estimate_time / $secs;

            if($d >= 1) {
                $r = round($d);

                /* Determine the language string needed */
                $language_string_time = $r > 1 ? Language::get()->global->date->{$str . 's'} : Language::get()->global->date->{$str};

                return $r . ' ' . $language_string_time . ' ' . Language::get()->global->date->time_ago;
            }
        }
    }

    /* Helper to have the time left from now to one point in time */
    public static function get_time_until($date) {

        $estimate_time = (new \DateTime($date))->getTimestamp() - time();

        if($estimate_time < 1) {
            return Language::get()->global->date->now;
        }

        $condition = [
            12 * 30 * 24 * 60 * 60  =>  'year',
            30 * 24 * 60 * 60       =>  'month',
            24 * 60 * 60            =>  'day',
            60 * 60                 =>  'hour',
            60                      =>  'minute',
            1                       =>  'second'
        ];

        foreach($condition as $secs => $str) {
            $d = $estimate_time / $secs;

            if($d >= 1) {
                $r = round($d);

                /* Determine the language string needed */
                $language_string_time = $r > 1 ? Language::get()->global->date->{$str . 's'} : Language::get()->global->date->{$str};

                return $r . ' ' . $language_string_time . ' ' . Language::get()->global->date->time_until;
            }
        }
    }

}
