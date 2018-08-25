<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\CheckUserFriendsListJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function index()
    {
        if (!\Auth::check()) {
//            return redirect('login');
            return redirect('signin');
        }

        return view('layouts.profile', ['api_token' => \Auth::user() ? \Auth::user()->api_token : '']);
    }

    public function signin()
    {
        return view('layouts.signin');
    }

    public function login(\Request $request)
    {
        $oauth_url = config('app.vk.oauth_url');
        $redirect_uri = $oauth_url . '?'
            . 'client_id=' . config('app.vk.client_id')
            . '&redirect_uri=' . env('APP_URL') . '/verify'
            . '&display=' . config('app.vk.display')
            . '&scope=' . config('app.vk.scope')
            . '&response_type=code'
            . '&v=' . config('app.vk.v')
        ;

        return redirect($redirect_uri);
    }

    public function verify(\Request $request)
    {
        if ($code = $request::get('code')) {
            $access_token_url = config('app.vk.access_token_url');
            $get_token_url = $access_token_url . '?'
                . 'client_id=' . config('app.vk.client_id')
                . '&client_secret=' . config('app.vk.private_key')
                . '&redirect_uri=' . env('APP_URL') . '/verify'
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
                $user->api          = Str::random(60);
            }
            $user->access_token = $access_token;
            $user->save();

            \Auth::login($user);

            $get_friends_job = new CheckUserFriendsListJob($user->user_id);
            Bus::dispatchNow($get_friends_job);

            return redirect('/');
        } else {
            throw new \Exception(sprintf("Code not found: %s", var_export($request::all(), true)));
        }
    }
}
