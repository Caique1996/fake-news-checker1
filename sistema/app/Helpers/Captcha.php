<?php

namespace App\Helpers;

class Captcha
{

    public static function verify($captcha)
    {
        if (isLocalEnv()) {
            return true;
        }

        $post_data = http_build_query(
            array(
                'secret' => env('CAPTCHA_SECRET'),
                'response' => $captcha,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            )
        );
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data
            )
        );
        $context = stream_context_create($opts);
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        $result = json_decode($response);
        return $result->success;
    }

}
