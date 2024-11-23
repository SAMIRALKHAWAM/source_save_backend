<?php

namespace App\Http\Requests\Files;

use App\Enums\FileStatusEnum;
use App\Enums\GroupStatusEnum;
use App\Http\Requests\BaseRequest;
use App\Models\File;
use App\Models\GroupUser;
use Illuminate\Validation\Rule;

class CheckInRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'group_id' => [
                Rule::exists('groups', 'id')
                    ->whereNotNull('approved_by'),
                'required',
                'integer',
            ],
            'files' => 'array|required',
            'files.*' => [
                Rule::exists('files', 'id')
                    ->where('status', GroupStatusEnum::ACCEPTED)
                    ->where('availability', FileStatusEnum::AVAILABLE),
                'required',
                'integer',
                'distinct',
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'files' => $this->input('files', []),
        ]);
    }

    /**
     * Add custom validation logic after base rules are applied.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $files = $this->input('files');
            $groupId = $this->input('group_id');


            if (!$this->userBelongsToGroup($groupId)) {
                $validator->errors()->add(
                    'group_id',
                    'You are not authorized to access this group.'
                );
            }


            if (!$this->checkAllFilesBelongToGroupAndHaveStatus($files, $groupId)) {
                $validator->errors()->add(
                    'files',
                    'Not all files belong to the specified group or do not meet the required status and availability conditions.'
                );
            }
        });
    }

    /**
     * Check if all files belong to the specified group and have the required status and availability.
     */
    private function checkAllFilesBelongToGroupAndHaveStatus(array $files, int $groupId): bool
    {

        $matchingFilesCount = File::whereIn('id', $files)
            ->whereHas('GroupUser', function ($query) use ($groupId) {
                $query->where('group_id', $groupId);
            })
            ->count();


        return $matchingFilesCount === count($files);
    }

    /**
     * Check if the user belongs to the specified group.
     */
    private function userBelongsToGroup(int $groupId): bool
    {
        return GroupUser::where('group_id', $groupId)
            ->where('user_id', auth('user')->user()->id)->exists();
    }
}
