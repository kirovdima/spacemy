<?php

namespace App\Console\Commands;

use App\Jobs\CheckUserFriendsListJob;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckFriendsList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'friendsList:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Для каждого отслеживаемого пользователя проверяет изменение списка друзей';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::get();
        foreach ($users as $user) {
            if ($user->isGuest()) {
                continue;
            }

            CheckUserFriendsListJob::dispatch($user->user_id);
        }

        $user_friends = DB::select('
            SELECT u.user_id, uf.friend_id
            FROM users u 
              JOIN user_friends uf ON u.user_id = uf.user_id
        ');

        foreach ($user_friends as $user_friend) {
            CheckUserFriendsListJob::dispatch($user_friend->user_id, $user_friend->friend_id);
        }
    }
}
