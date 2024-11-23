<?php

namespace App\Http\Requests\Files;

use App\Enums\FileUpdateTypeEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckOutRequest extends BaseRequest
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
            'file_id' => [Rule::exists('files','id')->whereNotNull('reserved_by')->where('reserved_by',$user->id),'required','integer'],
            'update_type' => [Rule::in(FileUpdateTypeEnum::toArray()),'required'],
            'url' => 'required_if:update_type,update|file',
        ];
    }
}
