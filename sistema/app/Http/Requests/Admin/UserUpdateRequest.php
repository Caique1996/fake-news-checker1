<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    private ?User $user;

    public function __construct(?User $user)
    {
        $this->user = $user;
    }

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
        $rules = (new UserStoreRequest())->rules();
        $rules['password'] = 'confirmed';
        $email = request("email");
        if (is_null($this->user) || $email == $this->user->email || is_null($email)) {
            $rules['email'] = 'email';
        }
        return $rules;
    }


}
