<?php

if (!function_exists('wcorrect')) {
    function wcorrect($weight) {

        return preg_replace('|\.?0+$|', '', $weight);
    }
}