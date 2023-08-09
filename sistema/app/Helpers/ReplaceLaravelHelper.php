<?php

use Illuminate\Support\Env;

function trans($key = null, $replace = [], $locale = null, $autoCreate = true)
{
    if (is_null($key)) {
        return app('translator');
    }
    if (str_contains($key, "backpack::")) {
        return app('translator')->get($key, $replace, $locale);
    }
    $env = Env::get("APP_ENV", "production");
    if ($env != 'production' && $autoCreate) {
        $fileDir = base_path("lang/pt_BR.json");
        $json = file_get_contents($fileDir);
        $transArr = json_decode($json, true);
        if (!isset($transArr[$key])) {
            $transArr[$key] = $key;
            file_put_contents($fileDir, json_encode($transArr));
        }
    }
    return app('translator')->get($key, $replace, $locale);
}


