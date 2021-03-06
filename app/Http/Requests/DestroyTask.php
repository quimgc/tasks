<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroyTask extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

//        if($this->HasPermissionTo('destroy-tasks')) return true;
//        if ($this->owns('tasks')) return true;
//        return false;
        return Auth::user()->hasPermissionTo('list-tasks');

        //return Auth::user()->HasPermissionTo('destroy-tasks');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

        ];
    }
}
