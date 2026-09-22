<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('generate_signed_token')) {
    function generate_signed_token($secret_key, $expire_minutes = 5)
    {
        $timestamp = time();
        $expires = $timestamp + ($expire_minutes * 60);

        $data = "expires=$expires";
        $signature = hash_hmac('sha256', $data, $secret_key);

        return base64_encode("$data&sig=$signature");
    }
}
