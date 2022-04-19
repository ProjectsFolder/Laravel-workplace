<?php

namespace App\Http\Requests;

use App\Exceptions\Handler;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserCredentialsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'max:25|required|regex:/^[a-zA-Z0-9]+$/u|unique:users,name',
            'email' => 'max:25',
            'password' => 'max:25|required',
            'roles' => 'array'
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => trans('messages.auth_error.password'),
        ];
    }

    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        Handler::failed($validator->errors()->all());
    }
}
