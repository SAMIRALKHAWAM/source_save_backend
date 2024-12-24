<?php

namespace App\Http\Requests\File;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShowFileVersionsRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fileId' => [Rule::exists('files', 'id'), 'required', 'integer'],
        ];
    }
}
