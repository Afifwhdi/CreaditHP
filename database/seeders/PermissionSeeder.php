<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $perm = Permission::firstOrCreate(['name' => 'verify_payment', 'guard_name' => 'web']);
        $kasir = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        $kasir->givePermissionTo($perm);

        // assign ke admin default (opsional)
        $user = \App\Models\User::where('email', 'admin@example.com')->first();
        if ($user) {
            $user->givePermissionTo('verify_payment');
        }
    }
}
