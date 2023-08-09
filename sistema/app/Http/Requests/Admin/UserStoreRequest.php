<?php

namespace App\Http\Requests\Admin;

use App\Enums\BoolStatus;
use App\Enums\UserType;
use App\Models\User;
use App\Rules\ValidateCpfCnpjRule;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:5|max:255',
            'email' => 'required|email|unique:' . (new User())->getTable(),
            'type' => 'required|in:' . separateByCommas(UserType::getValues()),
            'document' => ['required', new ValidateCpfCnpjRule()],
            'status' => 'required|in:' . separateByCommas(BoolStatus::getValues()),
            'password' => 'required|confirmed'
        ];
    }


}
