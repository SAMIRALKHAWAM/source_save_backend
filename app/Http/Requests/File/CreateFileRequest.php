<?php

namespace App\Http\Requests\File;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFileRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'group_id' => [Rule::exists('groups', 'id')->whereNotNull('approved_by'), 'required', 'integer'],
            'name' => [Rule::unique('files'),'required'],
            'description' => 'nullable|string',
            'url' => 'required|file',
        ];
    }
}
