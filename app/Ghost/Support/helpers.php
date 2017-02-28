<?php

if (!function_exists('wcorrect')) {
    function wcorrect($weight) {

        return preg_replace('|\.?0+$|', '', $weight);
    }
}

if (!function_exists('tg_name_escape')) {
    function tg_name_escape($username) {
        return str_replace('_', '\_', $username);
    }
}