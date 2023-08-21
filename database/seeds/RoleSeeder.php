<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminPermissions = Permission::all();
        Role::updateOrCreate([
            'id' => 1,
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ])->permissions()->sync($adminPermissions->pluck('id'));
    }
}
