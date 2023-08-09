<?php

namespace App\Http\Requests\Admin;

use App\Enums\BoolStatus;
use App\Enums\ReviewCheckStatus;
use Illuminate\Foundation\Http\FormRequest;

class ReviewRequestStore extends FormRequest
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
            'search_id' => 'required|exists:searches,id',
            'user_id' => 'required|exists:users,id',
            'check_status' => 'required|in:' . implodeComma(ReviewCheckStatus::getValues()),
            'text' => 'required|min:255'
        ];
    }


}
