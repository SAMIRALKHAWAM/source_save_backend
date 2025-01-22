<?php

namespace App\Http\Requests\File;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class CompareRequest extends BaseRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'path1' => 'required',
            'path2' => 'required',
        ];
    }
}
