<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Buat Role dulu =====
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $userRole       = Role::firstOrCreate(['name' => 'user',        'guard_name' => 'web']);
        $parentRole     = Role::firstOrCreate(['name' => 'parent',      'guard_name' => 'web']);
        $childRole      = Role::firstOrCreate(['name' => 'child',       'guard_name' => 'web']);

        // ===== Super Admin =====
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );
        $admin->assignRole($superAdminRole);

        // ===== User Biasa =====
        $user = User::firstOrCreate(
            ['email' => 'user@admin.com'],
            [
                'name'     => 'User Account',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );
        $user->assignRole($userRole);

        // ===== Orangtua =====
        $parent = User::firstOrCreate(
            ['email' => 'orangtua@admin.com'],
            [
                'name'     => 'Orangtua Account',
                'password' => Hash::make('password'),
                'role'     => 'parent',
            ]
        );
        $parent->assignRole($parentRole);

        // ===== Anak 1 =====
        $child1 = User::firstOrCreate(
            ['email' => 'anak1@admin.com'],
            [
                'name'      => 'Anak Account 1',
                'password'  => Hash::make('password'),
                'role'      => 'child',
                'parent_id' => $parent->id,
            ]
        );
        $child1->assignRole($childRole);

        // ===== Anak 2 =====
        $child2 = User::firstOrCreate(
            ['email' => 'anak2@admin.com'],
            [
                'name'      => 'Anak Account 2',
                'password'  => Hash::make('password'),
                'role'      => 'child',
                'parent_id' => $parent->id,
            ]
        );
        $child2->assignRole($childRole);
    }
}
