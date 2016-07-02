<?php

if (!function_exists('wcorrect')) {
    function wcorrect($weight) {

        if (substr($weight, -1) == 0) {
            $weight = substr($weight, 0, -1);
        }

        if (substr($weight, 0, 1) != 0) {
            $weight = substr($weight, 0, -2);
        }

        return (string)$weight;
    }
}