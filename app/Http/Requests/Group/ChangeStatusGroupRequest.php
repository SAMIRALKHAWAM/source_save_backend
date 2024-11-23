<?php

namespace App\Http\Requests\Group;

use App\Enums\GroupStatusEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeStatusGroupRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'groupId' => [Rule::exists('groups', 'id')->where('approved_by', null), 'required'],
            'status' => [Rule::in(GroupStatusEnum::changeStatus()), 'required'],
        ];
    }
}
