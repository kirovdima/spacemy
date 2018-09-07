<?php

namespace App\Console\Commands;

use App\Jobs\CheckUserFriendsStatusJob;
use App\User;
use Illuminate\Console\Command;

class CheckFriendsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'friendsStatus:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Для каждого пользователя создает задачу, которая проверяет статусы его друзей';

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
        $users = User::all();
        foreach ($users as $user) {
            if ($user->isGuest()) {
                continue;
            }
            CheckUserFriendsStatusJob::dispatch($user);
        }
    }
}
