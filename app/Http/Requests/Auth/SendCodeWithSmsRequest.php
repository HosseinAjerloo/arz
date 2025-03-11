<?php

namespace App\Http\Requests\Auth;

use App\Rules\MobileFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class SendCodeWithSmsRequest extends FormRequest
{
    private $rulesCustom=[];
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
        $routeName=Route::current();
        if ($routeName->getName()=='post.forgotPassword')
        {
            $this->rulesCustom= [
               "required",new MobileFormat(),'exists:users,mobile'
            ];
        }
        else{
            $this->rulesCustom=[
              "required",new MobileFormat(),'unique:users,mobile'
            ];
        }
       return[
            "mobile"=>$this->rulesCustom
        ];
    }

    public function messages()
    {
            return ['mobile.exists'=>'شماره تلفن وارد شده در سامانه وجود ندارد'];
    }
}
