<?php

namespace App\Http\Requests\Admin;

use App\Enums\BoolStatus;
use App\Enums\ReviewCheckStatus;
use Illuminate\Foundation\Http\FormRequest;

class ReviewRequestUpdate extends FormRequest
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
            'status' => 'required|in:' . implodeComma(BoolStatus::getValues()),
        ];
    }

}
