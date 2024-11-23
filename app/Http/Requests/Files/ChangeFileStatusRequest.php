<?php

namespace App\Http\Requests\Files;

use App\Enums\GroupStatusEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeFileStatusRequest extends BaseRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file_id' => [Rule::exists('files','id')->where('status',GroupStatusEnum::PENDING),'required','integer'],
            'status' => [Rule::in(GroupStatusEnum::changeStatus()),'required'],
        ];
    }
}
