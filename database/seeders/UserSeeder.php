<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder creates additional users for testing purposes.
     * Note: Use AdminSeeder for complete admin panel setup with sample data.
     */
    public function run(): void
    {
        // Additional admin users for testing
        User::firstOrCreate(
            ['email' => 'super.admin@eweddingcard.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'type' => 'admin',
            ]
        );

        // Additional test users for development
        User::firstOrCreate(
            ['email' => 'test.user@eweddingcard.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
                'type' => 'user',
            ]
        );

        User::firstOrCreate(
            ['email' => 'demo.client@eweddingcard.com'],
            [
                'name' => 'Demo Client',
                'password' => Hash::make('password123'),
                'type' => 'user',
            ]
        );

        // Malaysian clients for testing
        User::firstOrCreate(
            ['email' => 'amirah.hassan@email.com'],
            [
                'name' => 'Amirah Hassan',
                'password' => Hash::make('password123'),
                'type' => 'user',
            ]
        );

        User::firstOrCreate(
            ['email' => 'daniel.lim@email.com'],
            [
                'name' => 'Daniel Lim Wei Ming',
                'password' => Hash::make('password123'),
                'type' => 'user',
            ]
        );

        User::firstOrCreate(
            ['email' => 'priya.sharma@email.com'],
            [
                'name' => 'Priya Sharma',
                'password' => Hash::make('password123'),
                'type' => 'user',
            ]
        );

        $this->command->info('Additional users created successfully!');
        $this->command->info('Created/Updated:');
        $this->command->info('- 1 Super Admin (super.admin@eweddingcard.com)');
        $this->command->info('- 5 Additional Test Users');
        $this->command->info('');
        $this->command->info('Note: For complete admin panel setup, run AdminSeeder instead.');
    }
} 