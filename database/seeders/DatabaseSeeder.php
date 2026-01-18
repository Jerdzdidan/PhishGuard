<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
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
            'is_active' => true,
            'content' => "
                <h4>Introduction</h4>
                <p>
                    Cybersecurity is the practice of protecting systems, networks, applications, and data from unauthorized access, misuse, disruption, or destruction. In a university, this covers learning platforms, email, grading systems, research repositories, and personal devices. The goal is to maintain confidentiality (only authorized people access data), integrity (data stays accurate and unaltered), and availability (information is ready when needed).
                    <br>
                    Technology alone isn't enough: many incidents begin when a person is tricked into clicking a link, opening a risky file, or sharing a one-time code. Effective programs combine technical controls (patching, anti-malware, MFA) with everyday habits—pausing before acting, verifying through known channels, and reporting suspicious activity—so issues can be contained quickly.
                </p>
                <h4>Best Practices</h4>
                <ul>
                    <li>Use strong, unique passwords and enable MFA.</li>
                    <li>Keep operating systems, browsers, and apps updated.</li>
                    <li>Lock shared/lab devices and sign out when finished.</li>
                    <li>Treat unexpected links/attachments cautiously; verify first.</li>
                    <li>Report anything suspicious through the official channel.</li>
                </ul>
                <h4>Lesson</h4>
                <p>
                    Think of cybersecurity as <b>technology + people + process</b>. Technical tools block many attacks, but attackers often exploit human behavior. Build default habits: (1) pause, (2) check the real sender or domain, (3) verify using a known portal or hotline, (4) report so responders can act. Practicing these steps sharply reduces risk and speeds up response.
                </p>
            ",
        ]);

        Quiz::factory()->create([
            'lesson_id' => 1,
            'title' => 'Introduction to Cybersecurity Quiz',
            'passing_score' => 50,
            'is_active' => true,
        ]);

        Question::factory()->create([
            'quiz_id' => 1,
            'question_text' => 'Which part of the CIA triad ensures information remains accurate and unaltered?',
            'order' => 1,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 1,
            'option_letter' => 'A',
            'answer_text' => 'Confidentiality',
            'is_correct' => false,
        ]);
        Answer::factory()->create([
            'question_id' => 1,
            'option_letter' => 'B',
            'answer_text' => 'Integrity',
            'is_correct' => true,
        ]);
        Answer::factory()->create([
            'question_id' => 1,
            'option_letter' => 'C',
            'answer_text' => 'Availability',
            'is_correct' => false,
        ]);
        Answer::factory()->create([
            'question_id' => 1,
            'option_letter' => 'D',
            'answer_text' => 'Authenticity',
            'is_correct' => false,
        ]);

        Question::factory()->create([
            'quiz_id' => 1,
            'question_text' => 'A classmate leaves a lab PC logged in. What should you do?',
            'order' => 2,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 2,
            'option_letter' => 'A',
            'answer_text' => 'Ignore it',
            'is_correct' => false,
        ]);
        Answer::factory()->create([
            'question_id' => 2,
            'option_letter' => 'B',
            'answer_text' => 'Browse the web',
            'is_correct' => false,
        ]);
        Answer::factory()->create([
            'question_id' => 2,
            'option_letter' => 'C',
            'answer_text' => 'Lock or sign out the session, then alert them',
            'is_correct' => true,
        ]);
        Answer::factory()->create([
            'question_id' => 2,
            'option_letter' => 'D',
            'answer_text' => 'Copy files for backup',
            'is_correct' => false,
        ]);

        Question::factory()->create([
            'quiz_id' => 1,
            'question_text' => 'Why is awareness needed in addition to antivirus?',
            'order' => 3,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 3,
            'option_letter' => 'A',
            'answer_text' => 'Antivirus replaces passwords',
            'is_correct' => false,
        ]);
        Answer::factory()->create([
            'question_id' => 3,
            'option_letter' => 'B',
            'answer_text' => 'Many attacks rely on tricking people',
            'is_correct' => true,
        ]);
        Answer::factory()->create([
            'question_id' => 3,
            'option_letter' => 'C',
            'answer_text' => 'Antivirus blocks every risk',
            'is_correct' => false,
        ]);
        Answer::factory()->create([
            'question_id' => 3,
            'option_letter' => 'D',
            'answer_text' => 'Awareness is optional',
            'is_correct' => false,
        ]);

        Question::factory()->create([
            'quiz_id' => 1,
            'question_text' => 'On a shared campus PC, you should always:',
            'order' => 4,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 4,
            'option_letter' => 'A',
            'answer_text' => 'Save passwords in the browser',
            'is_correct' => false,
        ]);
        Answer::factory()->create([
            'question_id' => 4,
            'option_letter' => 'B',
            'answer_text' => 'Stay logged in for convenience',
            'is_correct' => false,
        ]);
        Answer::factory()->create([
            'question_id' => 4,
            'option_letter' => 'C',
            'answer_text' => 'Log out when done',
            'is_correct' => true,
        ]);
        Answer::factory()->create([
            'question_id' => 4,
            'option_letter' => 'D',
            'answer_text' => 'Disable updates',
            'is_correct' => false,
        ]);

        Question::factory()->create([
            'quiz_id' => 1,
            'question_text' => 'In the CIA triad, the “A” stands for:',
            'order' => 5,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 5,
            'option_letter' => 'A',
            'answer_text' => 'Authentication',
            'is_correct' => false,
        ]);
        Answer::factory()->create([
            'question_id' => 5,
            'option_letter' => 'B',
            'answer_text' => 'Authorization',
            'is_correct' => false,
        ]);
        Answer::factory()->create([
            'question_id' => 5,
            'option_letter' => 'C',
            'answer_text' => 'Availability',
            'is_correct' => true,
        ]);
        Answer::factory()->create([
            'question_id' => 5,
            'option_letter' => 'D',
            'answer_text' => 'Audit',
            'is_correct' => false,
        ]);

        Lesson::factory()->create([
            'image_path' => '',
            'title' => 'Social Engineering Fundamentals',
            'description' => 'An introductory lesson for cybersecurity fundamentals',
            'time' => 15,
            'difficulty' => 'EASY',
            'is_active' => true,
            'content' => "
                <h4>Introduction</h4>
                <p>
                   <b>Social engineering</b> uses influence and deception to make people perform unsafe actions—click a link, open a file, share an OTP, or send money. It works because everyday communication relies on trust and speed. If a message looks familiar, urgent, or authoritative, people may react before they verify. Building a consistent response habit is critical.
                </p>
                <h4>Best Practices</h4>
                <ul>
                    <li>Watch for psychological triggers: <b>authority, urgency, scarcity, reciprocity, fear.</b></li>
                    <li>Default to <b>Acknowledge → Contain → Notify</b> (pause, capture evidence, report).</li>
                    <li>Verify identities via <b>known</b> channels (official portal/hotline), not contacts inside the message.</li>
                    <li>Never share OTPs, PINs, or passwords via email, chat, or calls.</li>
                </ul>
                <h4>Lesson</h4>
                <p>
                    Attackers “hack” people, not just machines. Typical lures impersonate IT support, banks, or school offices; push short deadlines; promise rewards; or warn of penalties. Your safe sequence:
                </p>
                <ol>
                    <li><b>Acknowledge</b> (don't interact),</li>
                    <li><b>Contain</b> (screenshot sender and indicators, preserve the message),</li>
                    <li>
                        <b>Notify</b> (use the official report button/mailbox). If money or accounts are at risk, do an out-of-band check using a number or portal you already trust.
                    </li>
                </ol>
            ",
        ]);

        Lesson::factory()->create([
            'image_path' => '',
            'title' => 'Phishing, Smishing, and Vishing',
            'description' => 'An introductory lesson for cybersecurity fundamentals',
            'time' => 15,
            'difficulty' => 'MEDIUM',
            'is_active' => true,
            'content' => "
                <h4>Introduction</h4>
                <p>
                   Phishing (email), smishing (SMS/IM), and vishing (voice calls) are different channels with one goal: persuade you to act unsafely. Each channel has distinct red flags and matching safe responses.
                </p>
                <h4>Best Practices</h4>
                <ul>
                    <li><b>Email:</b> Preview links, check real sender domain, beware of HTML/ZIP/EXE attachments, report.</li>
                    <li><b>SMS/IM:</b> Don't tap shortened/odd domains; screenshot + block; report; verify only via official app/portal.</li>
                    <li><b>Voice:</b> Never read OTPs by phone; hang up; call the official hotline yourself; report.</li>
                </ul>
                <h4>Lesson</h4>
                <p>
                    Email often hides fake sign-in links or uses look-alike domains. SMS/IM lures push tiny urgent fees with strange or shortened domains. Phone calls may spoof names/numbers and ask for immediate codes. Apply channel-specific checks, then always preserve evidence and notify the right team. MFA and updates reduce the blast radius if a mistake occurs.
                </p>
            ",
        ]);

        Lesson::factory()->create([
            'image_path' => '',
            'title' => 'Social Media Impersonation & Hijacked Accounts',
            'description' => 'An introductory lesson for cybersecurity fundamentals',
            'time' => 15,
            'difficulty' => 'MEDIUM',
            'is_active' => true,
            'content' => "
                <h4>Introduction</h4>
                <p>
                   Attackers create fake profiles or hijack real ones to trick contacts into sending money, codes, or sensitive data. Recognizing profile signals and verifying identity through known channels are key to staying safe.
                </p>
                <h4>Best Practices</h4>
                <ul>
                    <li>Check profile age, handle, posting history, and inconsistencies.</li>
                    <li>Verify identity via a known channel (call or official contact), not within the chat.</li>
                    <li>If your account is hijacked: change password, end unknown sessions, enable MFA, notify contacts.</li>
                    <li>Report fake profiles/chats and keep screenshots.</li>
                </ul>
                <h4>Lesson</h4>
                <p>
                    Impostors rely on familiarity: duplicate names/photos, urgent requests, and private channels. Treat every unexpected request as potential social engineering: pause, verify off-channel, preserve evidence, and report. If you're the victim, speed matters—regain control, cut off attacker sessions, turn on MFA, and warn contacts so the scam stops spreading.
                </p>
            ",
        ]);

        Lesson::factory()->create([
            'image_path' => '',
            'title' => 'Identity Theft & Data Protection',
            'description' => 'An introductory lesson for cybersecurity fundamentals',
            'time' => 15,
            'difficulty' => 'HARD',
            'is_active' => true,
            'content' => "
                <h4>Introduction</h4>
                <p>
                   Identity theft happens when someone uses another person's identifiers—names, ID numbers, photos of IDs, passwords, or account details—without permission. Stolen data is often reused to make later scams more convincing. Limiting what you share and using approved channels greatly reduces risk.
                </p>
                <h4>Best Practices</h4>
                <ul>
                    <li>Follow minimal-PII: collect/share only what's necessary.</li>
                    <li>Submit documents via approved institutional portals; avoid personal email or chat for IDs.</li>
                    <li>Redact sensitive parts when full data isn't required.</li>
                    <li>Use strong passwords and MFA; watch for unfamiliar logins.</li>
                    <li>Report suspected exposure promptly.</li>
                </ul>
                <h4>Lesson</h4>
                <p>
                    Data leaks via phishing, unsafe forms, lost devices, weak/reused passwords, or oversharing. Once exposed, attackers reuse details (like student IDs or addresses) to personalize future scams. Mitigate by limiting data shared, verifying destinations, and preferring systems with access controls and audit logs. If you suspect exposure, change passwords, enable MFA, and file a clear incident report (what happened, when, which data) so responders can assist.
                </p>
            ",
        ]);
    }
}
