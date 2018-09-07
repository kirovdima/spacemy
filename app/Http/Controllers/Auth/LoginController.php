<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\CheckUserFriendsListJob;
use App\MongoModels\VkFriend;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function index()
    {
        if (!\Auth::check()) {
            return redirect('signin');
        }

        return view('layouts.profile', ['api_token' => Auth::user() ? Auth::user()->api_token : '']);
    }

    public function signin()
    {
        return view(
            'layouts.signin', [
                'api_token'        => User::GUEST_API_TOKEN,
                'random_person_id' => 12922845,
            ]
        );
    }

    public function login()
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

    public function verify(Request $request)
    {
        $this->validate($request, [
            'code' => 'required'
        ]);

        $code = $request->get('code');
        $access_token_url = config('app.vk.access_token_url');
        $get_token_url = $access_token_url . '?'
            . 'client_id=' . config('app.vk.client_id')
            . '&client_secret=' . config('app.vk.private_key')
            . '&redirect_uri=' . env('APP_URL') . '/verify'
            . '&code=' . $code
        ;

        $res = (new \GuzzleHttp\Client())
            ->request('GET', $get_token_url)
        ;
        $json = json_decode($res->getBody(), true);

        $user_id      = $json['user_id'];
        $access_token = $json['access_token'];
        $email        = $json['email'] ?? null;

        $user = User::find($user_id);
        if (!$user) {
            $user = new User();
            $user->user_id      = $user_id;
            $user->api_token    = Str::random(60);
        }
        $user->email        = $email;
        $user->access_token = $access_token;
        $user->save();

        Auth::login($user);

        if ([] == VkFriend::getFriends($user->user_id)) {
            $get_friends_job = new CheckUserFriendsListJob($user->user_id);
            Bus::dispatchNow($get_friends_job);
        }

        return redirect('/');
    }
}
