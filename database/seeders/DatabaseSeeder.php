<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::factory()->create([
            'first_name' => 'root',
            'last_name' => 'user',
            'email' => 'root@gmail.com',
            'user_type' => 'ADMIN',
            'password' => '123456'
        ]);

        User::factory()->create([
            'first_name' => 'test',
            'last_name' => 'user',
            'email' => 'testuser@gmail.com',
            'user_type' => 'USER',
            'password' => '123456'
        ]);
        
        Lesson::factory()->create([
            'image_path' => '',
            'name' => 'Introduction to Cybersecurity',
            'description' => 'An introductory lesson for cybersecurity fundamentals',
            'time' => 15,
            'difficulty' => 'EASY',
        ]);
    }
}
