<?php

namespace App\Http\Requests\Files;

use App\Enums\GroupStatusEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetFilesRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'group_id' => [Rule::exists('groups','id')->whereNotNull('approved_by'),'required','integer'],
            'status' => [Rule::in(GroupStatusEnum::allStatus()),'nullable'],
        ];
    }
}
