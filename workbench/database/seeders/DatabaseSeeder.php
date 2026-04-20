<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Workbench\App\Models\TestDate;
use Workbench\App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test admin user for Filament login.
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ],
        );

        // 2024-09-11 Gregorian → 2017-01-01 Ethiopic (New Year boundary)
        TestDate::query()->updateOrCreate(
            ['birth_date' => '2024-09-11'],
        );

        // 2023-09-12 Gregorian → 2016-01-01 Ethiopic (New Year boundary)
        TestDate::query()->updateOrCreate(
            ['birth_date' => '2023-09-12'],
        );
    }
}
