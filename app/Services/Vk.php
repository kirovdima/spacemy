<?php

namespace App\Services;

use App\ResponseLog;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class Vk
{
    private $http_client = null;

    public function __construct()
    {
        $this->http_client = new Client();
    }

    public function request($method_name, $access_token, $params)
    {
        $params = array_merge(
            [
                'access_token' => $access_token,
                'v'            => config('app.vk.v'),
            ],
            $params
        );


        $url = config('app.vk.api_url')
            . '/' . $method_name
            . '?' . http_build_query($params)
        ;

        /** @var Response $res */
        $res = $this->http_client->request('GET', $url);
        $resArr = json_decode($res->getBody(), true);

        $responseLog = new ResponseLog();
        $responseLog->method     = $method_name;
        $responseLog->parameters = print_r($params, true);
        $responseLog->status     = isset($resArr['error']) ? ResponseLog::STATUS_ERROR : ResponseLog::STATUS_OK;
        if (isset($resArr['error'])) {
            $responseLog->error_code = $resArr['error']['error_code'];
            $responseLog->error_msg  = $resArr['error']['error_msg'];
        }
        $responseLog->save();

        if (isset($resArr['error'])) {
            return false;
        }

        return $resArr['response'];
    }
}
