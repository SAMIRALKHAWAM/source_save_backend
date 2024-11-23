<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Permission::create([
            'name' => 'Add_File',
            'guard_name' => 'user',
        ]);

        Permission::create([
            'name' => 'Edit_File',
            'guard_name' => 'user',
        ]);

        Permission::create([
            'name' => 'Delete_File',
            'guard_name' => 'user',
        ]);

    }
}
