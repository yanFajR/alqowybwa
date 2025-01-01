<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerRole = Role::create(['name' => 'owner']);
        $studentRole = Role::create(['name' => 'student']);
        $teacherRole = Role::create(['name' => 'teacher']);

        $userOwner = User::create([
            'name' => 'Ryan Fajri',
            'occupation' => 'IT Engineer',
            'avatar' => 'images/default-avatar.png',
            'email' => 'imryanfajri@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $userOwner->assignRole($ownerRole);
    }
}
