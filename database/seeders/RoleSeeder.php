<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'super-admin' => 'Super Administrator',
            'admin' => 'Administrator Program',
            'verifier' => 'Verifikator',
            'approver' => 'Approver / Kepala',
            'treasurer' => 'Bendahara',
            'applicant' => 'Pendaftar',
        ];

        foreach ($roles as $name => $label) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Seed default super admin
        $superAdmin = \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@beasiswa.test'],
            [
                'name' => 'Super Admin',
                'nik' => '0000000000000001',
                'password' => bcrypt('password'),
                'phone' => '081234567890',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super-admin');
    }
}
