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
            'title' => 'Introduction to Cybersecurity',
            'description' => 'An introductory lesson for cybersecurity fundamentals',
            'time' => 15,
            'difficulty' => 'EASY',
            'content' => '
                <h4>Introduction</h4>
                <p>
                    Cybersecurity is the practice of protecting systems, networks, applications, and data from unauthorized access, misuse, disruption, or destruction. In a university, this covers learning platforms, email, grading systems, research repositories, and personal devices. The goal is to maintain confidentiality (only authorized people access data), integrity (data stays accurate and unaltered), and availability (information is ready when needed).
                    <br>
                    Technology alone isn\'t enough: many incidents begin when a person is tricked into clicking a link, opening a risky file, or sharing a one-time code. Effective programs combine technical controls (patching, anti-malware, MFA) with everyday habits—pausing before acting, verifying through known channels, and reporting suspicious activity—so issues can be contained quickly.
                </p>
                <h4>Best Practices</h4>
                <ul>
                    <li>Use strong, unique passwords and enable MFA.</li>
                    <li>Keep operating systems, browsers, and apps updated.</li>
                    <li>Lock shared/lab devices and sign out when finished.</li>
                    <li>Treat unexpected links/attachments cautiously; verify first.</li>
                    <li>Report anything suspicious through the official channel.</li>
                </ul>
                <h4>Best Practices</h4>
                <p>
                    Think of cybersecurity as <b>technology + people + process</b>. Technical tools block many attacks, but attackers often exploit human behavior. Build default habits: (1) pause, (2) check the real sender or domain, (3) verify using a known portal or hotline, (4) report so responders can act. Practicing these steps sharply reduces risk and speeds up response.
                </p>
            ',
        ]);
    }
}
