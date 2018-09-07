<?php

namespace App\Services;

use App\Exceptions\Exception;
use App\ResponseLog;
use App\UserFriend;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;

class Vk
{
    const ERROR_CODE_USER_WAS_DELETED_OR_BANNED = 18;

    private $http_client = null;

    public function __construct()
    {
        $this->http_client = new Client();
    }

    /**
     * @param string $method_name
     * @param string $access_token
     * @param array  $params
     *
     * @return array|bool
     */
    private function request($method_name, $access_token, $params)
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
        $responseLog->parameters = json_encode($params);
        $responseLog->response   = $res->getBody();
        $responseLog->status     = isset($resArr['error']) ? ResponseLog::STATUS_ERROR : ResponseLog::STATUS_OK;
        if (isset($resArr['error'])) {
            $responseLog->error_code = $resArr['error']['error_code'];
            $responseLog->error_msg  = $resArr['error']['error_msg'];
        }
        $responseLog->save();

        if (isset($resArr['error'])) {
            return $resArr['error'];
        }

        return $resArr['response'];
    }

    /**
     * @param $user
     * @param null $owner_id
     *
     * @return array|bool
     * @throws Exception
     */
    public function getFriends($user, $owner_id = null)
    {
        $friends = $this->request(
            'friends.get',
            $user->access_token,
            [
                'user_id' => $owner_id ?: $user->user_id,
                'fields'  => 'id,first_name,last_name,photo_50'
            ]
        );

        if (isset($friends['error_code'])) {
            switch ($friends['error_code']) {
                case self::ERROR_CODE_USER_WAS_DELETED_OR_BANNED:
                    if ($owner_id) {
                        $user_friend = UserFriend::getByUserIdAndPersonId($user->user_id, $owner_id);
                        $user_friend->disable($friends['error_msg']);
                    }
                    break;
                default:
                    Log::error(sprintf("undefined error_code: %s, error_message: %s", $friends['error_code'], $friends['error_msg']));
                    break;
            }
            return false;
        }

        return $friends;
    }

    /**
     * @param $user
     * @param array $person_ids
     *
     * @return array|bool
     */
    public function getUsers($user, array $person_ids)
    {
        $users = $this->request(
            'users.get',
            $user->access_token,
            [
                'user_ids' => implode(',', $person_ids),
                'fields'   => 'id,first_name,last_name,photo_50,online,last_seen',
            ]
        );

        if (isset($users['error_code'])) {
            Log::error(sprintf("undefined error_code: %s, error_message: %s", $users['error_code'], $users['error_msg']));
            return false;
        }

        return $users;
    }
}
