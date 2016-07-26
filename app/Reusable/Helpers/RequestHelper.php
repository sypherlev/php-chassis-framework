<?php

namespace MyApp\Reusable\Helpers;

class RequestHelper
{
    public function curlRequestJSONResponse($url, $headers = []) {
        $curl = curl_init();
        $additional = array('Content-type: application/json');
        $additional = array_merge($additional, $headers);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $additional);
        curl_setopt($curl, CURLOPT_POST,true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }
}