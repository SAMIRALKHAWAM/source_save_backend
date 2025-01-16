<?php

namespace App\Http\Requests\User\Auth;

use App\Http\Requests\BaseRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserLoginRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [ function ($attribute, $value, $fail) {
                $userExists = User::where('email', $value)->exists();
                $adminExists = Admin::where('email', $value)->exists();

                if (!$userExists && !$adminExists) {
                    $fail("The selected {$attribute} is invalid.");
                }
            }, 'required', 'email'],
            'password' => 'required|min:8',
            'fcm_token' => 'nullable',
        ];
    }
}
