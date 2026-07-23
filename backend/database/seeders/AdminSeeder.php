<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@clipchopper.com'],
            [
                'name'     => 'ClipChopper Admin',
                'password' => Hash::make('ChangeMe123!'),
                'role'     => 'admin',
            ]
        );

        Admin::firstOrCreate(
            ['user_id' => $adminUser->id],
            ['is_superadmin' => true]
        );

        $demoUser = User::firstOrCreate(
            ['email' => 'demo.admin@clipchopper.com'],
            [
                'name'     => 'Demo Admin',
                'password' => Hash::make('DemoAdmin123!'),
                'role'     => 'admin',
            ]
        );

        Admin::firstOrCreate(
            ['user_id' => $demoUser->id],
            ['is_superadmin' => false]
        );

        $this->command->info('Admin users created:');
        $this->command->info('- admin@clipchopper.com / ChangeMe123!');
        $this->command->info('- demo.admin@clipchopper.com / DemoAdmin123!');
    }
}
