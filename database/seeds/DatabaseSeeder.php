<?php

use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\DistrictSeeder;
use Database\Seeders\DivisionSeeder;
use Database\Seeders\JobCloseRemarkSeeder;
use Database\Seeders\ThanasSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // RoleSeeder::class,
            // PermissionSeeder::class,
            // UserSeeder::class,
            // RolePermissionSeeder::class,
            // DistrictSeeder::class,
            // DivisionSeeder::class,
            // ThanasSeeder::class,
            JobCloseRemarkSeeder::class,
        ]);
    }
}
