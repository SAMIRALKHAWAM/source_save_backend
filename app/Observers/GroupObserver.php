<?php

namespace App\Observers;

use App\Enums\IsAdminEnum;
use App\Models\Group;
use App\Models\GroupUser;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GroupObserver
{

    /**
     * Handle the Group "created" event.
     */
    public function created(Group $group): void
    {

        $users = \request()->users;
        $permissions = \request()->permissions;
        $role = $this->AddGroupRoleAndAddPermissions($group->name, $permissions);
        $this->AddGroupUsers($group->id, $role->name, $users);
    }

    private function AddGroupRoleAndAddPermissions($name, $permissions)
    {
        $role = Role::create([
            'name' => $name,
            'guard_name' => 'user',
        ]);
        $permissions = Permission::whereIn('id', $permissions)->get();
        $role->givePermissionTo($permissions);
        return $role;
    }


    private function AddGroupUsers($id, $role, $users)
    {
        // Add Admin Of This Group
        $groupUser = GroupUser::create([
            'group_id' => $id,
            'user_id' => \auth('user')->user()->id,
            'is_admin' => IsAdminEnum::ADMIN,
        ]);
        $groupUser->User->assignRole([$role]);
        // Add Members to same group
        foreach ($users as $user) {
            $groupUser = GroupUser::create([
                'group_id' => $id,
                'user_id' => $user,
            ]);
            $groupUser->User->assignRole([$role]);
        }
    }




    /**
     * Handle the Group "updated" event.
     */
    public function updated(Group $group): void
    {
        //
    }


    /**
     * Handle the Group "deleted" event.
     */
    public function deleted(Group $group): void
    {
        //
    }




}
