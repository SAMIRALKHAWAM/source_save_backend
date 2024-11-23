<?php

namespace App\Http\Requests\Group;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateGroupRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = \auth('user')->user();
        return [
            'name' => [Rule::unique('groups'), 'string', 'required'],
            'permissions' => 'required|array',
            'permissions.*' => [Rule::exists('permissions', 'id'), 'required', 'integer', 'distinct'],
            'users' => 'required|array',
            'users.*' => [Rule::exists('users', 'id')->whereNot('id', $user->id), 'required', 'integer', 'distinct'],
        ];
    }
}
