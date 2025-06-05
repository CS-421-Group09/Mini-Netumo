<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Target;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
            'name' => 'Test User',
            'password' => bcrypt('password'),
            'slack_webhook_url' => 'https://hooks.slack.com/services/xxx/xxx/xxx'
            ]
        );

        // Create some test targets
        Target::create([
            'user_id' => $user->id,
            'name' => 'Google',
            'url' => 'https://google.com',
            'check_frequency' => 5
        ]);

        Target::create([
            'user_id' => $user->id,
            'name' => 'GitHub',
            'url' => 'https://github.com',
            'check_frequency' => 5
        ]);
    }
}
