<?php

namespace MyApp\External;

interface OAuth
{
    public function sendAuthRequest($request_token);
    public function getAuthToken();
}