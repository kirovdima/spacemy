<?php

namespace App\Http\Requests;

use App\Entity\Period\PeriodFactory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class VisitsStatisticRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $person_id  = $this->route('person_id');
        $period     = $this->route('period');
        $start_date = $this->route('start_date');

        return in_array($period, PeriodFactory::$allowed_period)
            && false !== strtotime($start_date)
            && (
                Auth::user()->isStatisticAvailable($person_id)
                || Auth::user()->isGuest()
            )
            ;
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
