<?php

namespace App\Http\Requests\Files;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetUserFilesRequest extends BaseRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
            return [
                'groupId' => [Rule::exists('groups', 'id'), 'required'],
                'userId' => [Rule::exists('users', 'id'), 'required'],
                'status' => [Rule::in(['free','reserved']),'required'],
            ];
    }
}
