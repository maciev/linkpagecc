<?php

function string_truncate($string, $maxchar) {
    $length = strlen($string);
    if($length > $maxchar) {
        $cutsize = -($length-$maxchar);
        $string  = substr($string, 0, $cutsize);
        $string  = $string . '..';
    }
    return $string;
}

function string_filter_alphanumeric($string) {

    $string = preg_replace('/[^a-zA-Z0-9\s]+/', '', $string);

    $string = preg_replace('/\s+/', ' ', $string);

    return $string;
}

function string_generate($length) {
    $characters = str_split('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
    $content = '';

    for($i = 1; $i <= $length; $i++) {
        $content .= $characters[array_rand($characters, 1)];
    }

    return $content;
}

function string_ends_with($needle, $haystack) {
    return substr($haystack, -strlen($needle)) === $needle;
}
