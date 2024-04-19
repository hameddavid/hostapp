<?php

if (!function_exists('generateSixRandomCharacter')) {
    function generateSixRandomCharacter($length = 6) {
        $characters = 'abcdefghijkmnpqrstuvwxyz23456789';
        $randomString = str_shuffle($characters);
        return substr($randomString, 0, $length);
    }
}