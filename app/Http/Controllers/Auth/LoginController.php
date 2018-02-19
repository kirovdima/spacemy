<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function verify(\Request $request)
    {
        if ($code = $request::get('code')) {
            $access_token_url = config('app.vk.access_token_url');
            $get_token_url = $access_token_url . '?'
                . 'client_id=' . config('app.vk.client_id')
                . '&client_secret=' . config('app.vk.private_key')
                . '&redirect_uri=http://spacemy.ru/verify'
                . '&code=' . $code
            ;
            $client = new \GuzzleHttp\Client();
            /** @var \GuzzleHttp\Psr7\Response $res */
            $res = $client->request('GET', $get_token_url);
            $json = json_decode($res->getBody(), true);

            $user_id      = $json['user_id'];
            $access_token = $json['access_token'];

            $user = \App\User::getModel()->where('user_id', $user_id)->first();
            if (!$user) {
                $user = new \App\User();
                $user->user_id      = $user_id;
                $user->access_token = $access_token;
                $user->save();
            } else {
                $user->access_token = $access_token;
                $user->save();
            }

            \Auth::login($user);

            return redirect('/');
        } else {
            throw new \Exception(sprintf("Code not found: %s", var_export($request::all(), true)));
        }
    }
}
