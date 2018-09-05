<?php

namespace App\Http\Requests;

use App\UserFriend;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddPersonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $person_id = $this->route('id');

        if (!Auth::user()->isFriend($person_id)) {
            return false;
        }

        if (UserFriend::getByUserIdAndPersonId(Auth::user()->user_id, $person_id)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
