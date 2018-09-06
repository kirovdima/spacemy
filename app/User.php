<?php

namespace App;

use App\MongoModels\VkFriend;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\User
 *
 * @mixin \Eloquent
 * @property int $user_id
 * @property string $access_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $api_token
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUserId($value)
 */
class User extends Model implements Authenticatable
{
    use \Illuminate\Auth\Authenticatable;

    const GUEST_API_TOKEN = 'dkZC434DFVZA667xCVbnh7fQwEzXDFBGH67Vghhg';

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $fillable = ['user_id', 'access_token', 'api_token'];

    /**
     * @return bool
     */
    public function isGuest()
    {
        return $this->api_token == self::GUEST_API_TOKEN;
    }

    /**
     * @param int $person_id
     * @return bool
     */
    public function isFriend(int $person_id)
    {
        $vk_friends = VkFriend::getFriends($this->user_id);
        if ([] === $vk_friends) {
            return false;
        }

        foreach ($vk_friends['friends'] as $friend) {
            if ($person_id == $friend['id']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $person_id
     * @return bool
     */
    public function isStatisticAvailable(int $person_id)
    {
        return (bool) UserFriend::getByUserIdAndPersonId($this->user_id, $person_id);
    }

    /**
     * @return int
     */
    public function watchingPersonsCount()
    {
        return UserFriend::getPersonsCount($this->user_id);
    }
}
