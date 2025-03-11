<?php

namespace App\Http\Requests\Auth;

use App\Rules\MobileFormat;
use Illuminate\Foundation\Http\FormRequest;

class SimpleLoginPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password'=>'required',
            "mobile"=>["required",new MobileFormat(),'exists:users,mobile'],
            'rememberMe'=>'nullable|in:on'

        ];
    }
    public function messages()
    {
        return [
            'mobile.exists'=>'اطلاعات وارد شده صحیح نمیباشد'
        ];
    }

}
