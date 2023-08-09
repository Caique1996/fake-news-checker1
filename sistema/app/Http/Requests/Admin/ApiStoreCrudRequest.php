<?php

namespace App\Http\Requests\Admin;


use App\Enums\ApiStatusEnum;
use App\Enums\BoolStatus;
use App\Rules\CheckHttpsUrl;
use App\Rules\ValidateIpList;
use App\Rules\ValidHttpsUrl;
use Illuminate\Foundation\Http\FormRequest;

class ApiStoreCrudRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'ip_whitelist' => ['required', new ValidateIpList()],
            'status' => 'required|in:' . implodeComma(BoolStatus::getValues()),
            'webhook_url' => ['required', new ValidHttpsUrl()]
        ];
    }

}
