<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\Concerns\Has;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::create([
            'name' => 'samir',
            'email' => 'samir@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $fileName = 'logs/user_logs/user_' . $user->id . '.log';
       $content = "User Registered: \n";
        Storage::put($fileName, $content);

        $user = User::create([
            'name' => 'samir',
            'email' => 'dd@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $fileName = 'logs/user_logs/user_' . $user->id . '.log';
        $content = "User Registered: \n";
        Storage::put($fileName, $content);

    }
}
