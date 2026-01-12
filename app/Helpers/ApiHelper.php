<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

class ApiHelper
{
    public static function getToken(): ?string
    {
        $token = Session::get('api_token');

        if (empty($token)) {
            $token = env('API_TOKEN');
        }

        return $token;
    }

    public static function setToken(string $token): void
    {
        Session::put('api_token', $token);
    }
}
