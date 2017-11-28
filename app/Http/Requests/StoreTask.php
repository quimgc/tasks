<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Quimgc\Tasks\Http\Requests\Traits\ChecksPermissions;

class StoreTask extends FormRequest
{
    use ChecksPermissions;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->hasPermissionTo('store-tasks')) return true;
        if (Auth::user()->id === $this->user_id) return true;
        return false;
        //return Auth::user()->HasPermissionTo('store-tasks');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return[
            'name'    => 'required',
            'user_id' => 'required',
        ];

    }
}
