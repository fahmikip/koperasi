<?php

namespace Database\Seeders;

use App\Models\SavingType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissions = collect(['members.view', 'members.create', 'members.update', 'members.delete', 'savings.view', 'savings.manage', 'loans.view', 'loans.manage', 'loans.create', 'loans.approve', 'loans.disburse', 'installments.manage', 'reports.view', 'users.manage', 'audit.view']);
        $permissions->each(fn ($name) => Permission::findOrCreate($name, 'web'));
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $roles = [
            'Super Admin' => $permissions->all(),
            'Admin' => $permissions->except($permissions->search('users.manage'))->all(),
            'Bendahara' => ['members.view', 'savings.view', 'savings.manage', 'loans.view', 'loans.create', 'loans.approve', 'loans.disburse', 'installments.manage', 'reports.view'],
            'Petugas' => ['members.view', 'members.create', 'members.update', 'savings.view', 'savings.manage', 'loans.view', 'loans.create', 'installments.manage'],
            'Anggota' => [],
        ];
        foreach ($roles as $name => $grants) {
            Role::findOrCreate($name, 'web')->syncPermissions($grants);
        }
        $admin = User::firstOrCreate(['email' => 'admin@koperasi.test'], ['name' => 'Super Admin', 'password' => 'password', 'email_verified_at' => now()]);
        $admin->syncRoles('Super Admin');
        foreach ([['Simpanan Pokok', 'once', 100000], ['Simpanan Wajib', 'monthly', 50000], ['Simpanan Sukarela', 'flexible', 0]] as [$name,$frequency,$amount]) {
            SavingType::firstOrCreate(compact('name'), ['frequency' => $frequency, 'default_amount' => $amount]);
        }
    }
}
