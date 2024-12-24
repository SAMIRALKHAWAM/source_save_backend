<?php

namespace App\Http\Requests\OldFile;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OldFileIdRequest extends BaseRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'old_file_id' => [Rule::exists('old_files', 'id'), 'required', 'integer'],
        ];
    }
}
