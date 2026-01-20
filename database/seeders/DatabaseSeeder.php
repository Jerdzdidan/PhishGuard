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
        
         
        // ============================================================
        // LESSON 1: Introduction to Cybersecurity (No Prerequisite)
        // ============================================================
        Lesson::factory()->create([
            'image_path' => '',
            'title' => 'Introduction to Cybersecurity',
            'description' => 'An introductory lesson for cybersecurity fundamentals',
            'time' => 15,
            'difficulty' => 'EASY',
            'is_active' => true,
            'prerequisite_lesson_id' => null, // First lesson - no prerequisite
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

        // Quiz for Lesson 1
        Quiz::factory()->create([
            'lesson_id' => 1,
            'title' => 'Introduction to Cybersecurity Quiz',
            'passing_score' => 80,
            'is_active' => true,
        ]);

        // Q1 - Lesson 1
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
            'explanation' => 'Incorrect: About secrecy, not correctness.'
        ]);
        Answer::factory()->create([
            'question_id' => 1,
            'option_letter' => 'B',
            'answer_text' => 'Integrity',
            'is_correct' => true,
            'explanation' => 'Correct: Integrity protects against unauthorized modification.'
        ]);
        Answer::factory()->create([
            'question_id' => 1,
            'option_letter' => 'C',
            'answer_text' => 'Availability',
            'is_correct' => false,
            'explanation' => 'Incorrect: Ensures systems/data are accessible when needed.'
        ]);
        Answer::factory()->create([
            'question_id' => 1,
            'option_letter' => 'D',
            'answer_text' => 'Authenticity',
            'is_correct' => false,
            'explanation' => 'Incorrect: Verifies identity/source, not data accuracy.'
        ]);

        // Q2 - Lesson 1
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
            'explanation' => 'Incorrect: Inaction leaves the account exposed.'
        ]);
        Answer::factory()->create([
            'question_id' => 2,
            'option_letter' => 'B',
            'answer_text' => 'Browse the web',
            'is_correct' => false,
            'explanation' => 'Incorrect: Misuse of someone else\'s session.'
        ]);
        Answer::factory()->create([
            'question_id' => 2,
            'option_letter' => 'C',
            'answer_text' => 'Lock or sign out the session, then alert them',
            'is_correct' => true,
            'explanation' => 'Correct: Prevents misuse and informs the owner.'
        ]);
        Answer::factory()->create([
            'question_id' => 2,
            'option_letter' => 'D',
            'answer_text' => 'Copy files for backup',
            'is_correct' => false,
            'explanation' => 'Incorrect: Copying files violates privacy and policy.'
        ]);

        // Q3 - Lesson 1
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
            'explanation' => 'Incorrect: Passwords and MFA are still required.'
        ]);
        Answer::factory()->create([
            'question_id' => 3,
            'option_letter' => 'B',
            'answer_text' => 'Many attacks rely on tricking people',
            'is_correct' => true,
            'explanation' => 'Correct: Social engineering bypasses tools by targeting people.'
        ]);
        Answer::factory()->create([
            'question_id' => 3,
            'option_letter' => 'C',
            'answer_text' => 'Antivirus blocks every risk',
            'is_correct' => false,
            'explanation' => 'Incorrect: No tool blocks everything.'
        ]);
        Answer::factory()->create([
            'question_id' => 3,
            'option_letter' => 'D',
            'answer_text' => 'Awareness is optional',
            'is_correct' => false,
            'explanation' => 'Incorrect: Awareness is essential, not optional.'
        ]);

        // Q4 - Lesson 1
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
            'explanation' => 'Incorrect: Saved passwords on shared machines are risky.'
        ]);
        Answer::factory()->create([
            'question_id' => 4,
            'option_letter' => 'B',
            'answer_text' => 'Stay logged in for convenience',
            'is_correct' => false,
            'explanation' => 'Incorrect: Leaves your account exposed.'
        ]);
        Answer::factory()->create([
            'question_id' => 4,
            'option_letter' => 'C',
            'answer_text' => 'Log out when done',
            'is_correct' => true,
            'explanation' => 'Correct: Ends access and protects your data.'
        ]);
        Answer::factory()->create([
            'question_id' => 4,
            'option_letter' => 'D',
            'answer_text' => 'Disable updates',
            'is_correct' => false,
            'explanation' => 'Incorrect: Updates patch vulnerabilities.'
        ]);

        // Q5 - Lesson 1
        Question::factory()->create([
            'quiz_id' => 1,
            'question_text' => 'In the CIA triad, the "A" stands for:',
            'order' => 5,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 5,
            'option_letter' => 'A',
            'answer_text' => 'Authentication',
            'is_correct' => false,
            'explanation' => 'Incorrect: Related security concept, but not part of the CIA acronym.'
        ]);
        Answer::factory()->create([
            'question_id' => 5,
            'option_letter' => 'B',
            'answer_text' => 'Authorization',
            'is_correct' => false,
            'explanation' => 'Incorrect: Related security concept, but not part of the CIA acronym.'
        ]);
        Answer::factory()->create([
            'question_id' => 5,
            'option_letter' => 'C',
            'answer_text' => 'Availability',
            'is_correct' => true,
            'explanation' => 'Correct: Availability is the "A" in CIA.'
        ]);
        Answer::factory()->create([
            'question_id' => 5,
            'option_letter' => 'D',
            'answer_text' => 'Audit',
            'is_correct' => false,
            'explanation' => 'Incorrect: Auditing is important but not in CIA.'
        ]);

        // ============================================================
        // LESSON 2: Social Engineering Fundamentals (Prerequisite: Lesson 1)
        // ============================================================
        Lesson::factory()->create([
            'image_path' => '',
            'title' => 'Social Engineering Fundamentals',
            'description' => 'Learn about social engineering tactics and how to defend against them',
            'time' => 15,
            'difficulty' => 'EASY',
            'is_active' => true,
            'prerequisite_lesson_id' => 1, // Requires Lesson 1
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
                    Attackers \"hack\" people, not just machines. Typical lures impersonate IT support, banks, or school offices; push short deadlines; promise rewards; or warn of penalties. Your safe sequence:
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

        // Quiz for Lesson 2
        Quiz::factory()->create([
            'lesson_id' => 2,
            'title' => 'Social Engineering Fundamentals Quiz',
            'passing_score' => 80,
            'is_active' => true,
        ]);

        // Q1 - Lesson 2
        Question::factory()->create([
            'quiz_id' => 2,
            'question_text' => 'Social engineering primarily targets:',
            'order' => 1,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 6,
            'option_letter' => 'A',
            'answer_text' => 'Software bugs',
            'is_correct' => false,
            'explanation' => 'Incorrect: These are technical elements.'
        ]);
        Answer::factory()->create([
            'question_id' => 6,
            'option_letter' => 'B',
            'answer_text' => 'Human behavior',
            'is_correct' => true,
            'explanation' => 'Correct: SE manipulates people to take unsafe actions.'
        ]);
        Answer::factory()->create([
            'question_id' => 6,
            'option_letter' => 'C',
            'answer_text' => 'Network cables',
            'is_correct' => false,
            'explanation' => 'Incorrect: These are technical elements.'
        ]);
        Answer::factory()->create([
            'question_id' => 6,
            'option_letter' => 'D',
            'answer_text' => 'Power supply',
            'is_correct' => false,
            'explanation' => 'Incorrect: These are technical elements.'
        ]);

        // Q2 - Lesson 2
        Question::factory()->create([
            'quiz_id' => 2,
            'question_text' => 'First safe action when a message feels wrong:',
            'order' => 2,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 7,
            'option_letter' => 'A',
            'answer_text' => 'Click to check details',
            'is_correct' => false,
            'explanation' => 'Incorrect: Interaction increases risk.'
        ]);
        Answer::factory()->create([
            'question_id' => 7,
            'option_letter' => 'B',
            'answer_text' => 'Reply to ask questions',
            'is_correct' => false,
            'explanation' => 'Incorrect: Interaction increases risk.'
        ]);
        Answer::factory()->create([
            'question_id' => 7,
            'option_letter' => 'C',
            'answer_text' => 'Pause and avoid interacting',
            'is_correct' => true,
            'explanation' => 'Correct: Pausing prevents mistakes and allows evidence capture.'
        ]);
        Answer::factory()->create([
            'question_id' => 7,
            'option_letter' => 'D',
            'answer_text' => 'Forward to everyone',
            'is_correct' => false,
            'explanation' => 'Incorrect: Spreading may cause more harm.'
        ]);

        // Q3 - Lesson 2
        Question::factory()->create([
            'quiz_id' => 2,
            'question_text' => 'Which is an authority cue?',
            'order' => 3,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 8,
            'option_letter' => 'A',
            'answer_text' => 'Limited slots!',
            'is_correct' => false,
            'explanation' => 'Incorrect: Scarcity/urgency/reciprocity, not authority.'
        ]);
        Answer::factory()->create([
            'question_id' => 8,
            'option_letter' => 'B',
            'answer_text' => 'IT Security Office',
            'is_correct' => true,
            'explanation' => 'Correct: Invokes institutional authority to pressure compliance.'
        ]);
        Answer::factory()->create([
            'question_id' => 8,
            'option_letter' => 'C',
            'answer_text' => 'Only today',
            'is_correct' => false,
            'explanation' => 'Incorrect: Scarcity/urgency/reciprocity, not authority.'
        ]);
        Answer::factory()->create([
            'question_id' => 8,
            'option_letter' => 'D',
            'answer_text' => 'Claim reward',
            'is_correct' => false,
            'explanation' => 'Incorrect: Scarcity/urgency/reciprocity, not authority.'
        ]);

        // Q4 - Lesson 2
        Question::factory()->create([
            'quiz_id' => 2,
            'question_text' => 'Verifying by calling the number inside the suspicious message is safe.',
            'order' => 4,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 9,
            'option_letter' => 'A',
            'answer_text' => 'True',
            'is_correct' => false,
            'explanation' => 'Incorrect: Numbers in the message may be attacker-controlled.'
        ]);
        Answer::factory()->create([
            'question_id' => 9,
            'option_letter' => 'B',
            'answer_text' => 'False',
            'is_correct' => true,
            'explanation' => 'Correct: Use a known hotline or official portal.'
        ]);

        // Q5 - Lesson 2
        Question::factory()->create([
            'quiz_id' => 2,
            'question_text' => 'In the safe sequence, Contain means:',
            'order' => 5,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 10,
            'option_letter' => 'A',
            'answer_text' => 'Delete immediately',
            'is_correct' => false,
            'explanation' => 'Incorrect: Deletion destroys evidence.'
        ]);
        Answer::factory()->create([
            'question_id' => 10,
            'option_letter' => 'B',
            'answer_text' => 'Capture evidence and preserve the message',
            'is_correct' => true,
            'explanation' => 'Correct: Evidence supports analysis and response.'
        ]);
        Answer::factory()->create([
            'question_id' => 10,
            'option_letter' => 'C',
            'answer_text' => 'Share in a group chat',
            'is_correct' => false,
            'explanation' => 'Incorrect: Spreading or ignoring is unsafe.'
        ]);
        Answer::factory()->create([
            'question_id' => 10,
            'option_letter' => 'D',
            'answer_text' => 'Ignore it',
            'is_correct' => false,
            'explanation' => 'Incorrect: Spreading or ignoring is unsafe.'
        ]);

        // ============================================================
        // LESSON 3: Phishing, Smishing, and Vishing (Prerequisite: Lesson 2)
        // ============================================================
        Lesson::factory()->create([
            'image_path' => '',
            'title' => 'Phishing, Smishing, and Vishing',
            'description' => 'Understand different types of phishing attacks and how to identify them',
            'time' => 15,
            'difficulty' => 'MEDIUM',
            'is_active' => true,
            'prerequisite_lesson_id' => 2, // Requires Lesson 2
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

        // Quiz for Lesson 3
        Quiz::factory()->create([
            'lesson_id' => 3,
            'title' => 'Phishing, Smishing, and Vishing Quiz',
            'passing_score' => 80,
            'is_active' => true,
        ]);

        // Q1 - Lesson 3
        Question::factory()->create([
            'quiz_id' => 3,
            'question_text' => 'A text asks you to pay a small delivery fee via a shortened or odd-looking domain. First safe step?',
            'order' => 1,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 11,
            'option_letter' => 'A',
            'answer_text' => 'Tap the link',
            'is_correct' => false,
            'explanation' => 'Incorrect: Tapping/paying can lead to theft or malware.'
        ]);
        Answer::factory()->create([
            'question_id' => 11,
            'option_letter' => 'B',
            'answer_text' => 'Pay to avoid delay',
            'is_correct' => false,
            'explanation' => 'Incorrect: Tapping/paying can lead to theft or malware.'
        ]);
        Answer::factory()->create([
            'question_id' => 11,
            'option_letter' => 'C',
            'answer_text' => 'Screenshot and report; then verify using the courier\'s official app/site',
            'is_correct' => true,
            'explanation' => 'Correct: Evidence + report + trusted verification.'
        ]);
        Answer::factory()->create([
            'question_id' => 11,
            'option_letter' => 'D',
            'answer_text' => 'Reply "STOP"',
            'is_correct' => false,
            'explanation' => 'Incorrect: Replies can confirm your number to attackers.'
        ]);

        // Q2 - Lesson 3
        Question::factory()->create([
            'quiz_id' => 3,
            'question_text' => 'A caller claims to be from a bank and requests your OTP to "reverse a charge." Best action?',
            'order' => 2,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 12,
            'option_letter' => 'A',
            'answer_text' => 'Provide the OTP once',
            'is_correct' => false,
            'explanation' => 'Incorrect: Never share OTP.'
        ]);
        Answer::factory()->create([
            'question_id' => 12,
            'option_letter' => 'B',
            'answer_text' => 'Keep talking',
            'is_correct' => false,
            'explanation' => 'Incorrect: Prolongs pressure.'
        ]);
        Answer::factory()->create([
            'question_id' => 12,
            'option_letter' => 'C',
            'answer_text' => 'Hang up and call the official hotline printed on the card/site',
            'is_correct' => true,
            'explanation' => 'Correct: Independent verification blocks spoofing.'
        ]);
        Answer::factory()->create([
            'question_id' => 12,
            'option_letter' => 'D',
            'answer_text' => 'Ask for an email',
            'is_correct' => false,
            'explanation' => 'Incorrect: Email can also be spoofed; still not independent.'
        ]);

        // Q3 - Lesson 3
        Question::factory()->create([
            'quiz_id' => 3,
            'question_text' => 'Which email element is the strongest red flag in a bank alert?',
            'order' => 3,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 13,
            'option_letter' => 'A',
            'answer_text' => 'Correct logo',
            'is_correct' => false,
            'explanation' => 'Incorrect: Easy to copy; weaker indicators.'
        ]);
        Answer::factory()->create([
            'question_id' => 13,
            'option_letter' => 'B',
            'answer_text' => 'Your name in the greeting',
            'is_correct' => false,
            'explanation' => 'Incorrect: Easy to copy; weaker indicators.'
        ]);
        Answer::factory()->create([
            'question_id' => 13,
            'option_letter' => 'C',
            'answer_text' => 'A look-alike domain that adds "-login.net" to the real bank name',
            'is_correct' => true,
            'explanation' => 'Correct: Domain authenticity is the best signal.'
        ]);
        Answer::factory()->create([
            'question_id' => 13,
            'option_letter' => 'D',
            'answer_text' => 'A footer with a physical address',
            'is_correct' => false,
            'explanation' => 'Incorrect: Easy to copy; weaker indicators.'
        ]);

        // Q4 - Lesson 3
        Question::factory()->create([
            'quiz_id' => 3,
            'question_text' => 'After reporting a smishing attempt, safest follow-up:',
            'order' => 4,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 14,
            'option_letter' => 'A',
            'answer_text' => 'Block the sender and verify status in the official app/site',
            'is_correct' => true,
            'explanation' => 'Correct: Stops more messages and verifies safely.'
        ]);
        Answer::factory()->create([
            'question_id' => 14,
            'option_letter' => 'B',
            'answer_text' => 'Reply for confirmation',
            'is_correct' => false,
            'explanation' => 'Incorrect: Engaging/spreading/ignoring doesn\'t confirm safety.'
        ]);
        Answer::factory()->create([
            'question_id' => 14,
            'option_letter' => 'C',
            'answer_text' => 'Share the link with friends',
            'is_correct' => false,
            'explanation' => 'Incorrect: Engaging/spreading/ignoring doesn\'t confirm safety.'
        ]);
        Answer::factory()->create([
            'question_id' => 14,
            'option_letter' => 'D',
            'answer_text' => 'Do nothing further',
            'is_correct' => false,
            'explanation' => 'Incorrect: Engaging/spreading/ignoring doesn\'t confirm safety.'
        ]);

        // Q5 - Lesson 3
        Question::factory()->create([
            'quiz_id' => 3,
            'question_text' => 'Caller-ID proves the caller is legitimate.',
            'order' => 5,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 15,
            'option_letter' => 'A',
            'answer_text' => 'True',
            'is_correct' => false,
            'explanation' => 'Incorrect: Caller-ID can be spoofed.'
        ]);
        Answer::factory()->create([
            'question_id' => 15,
            'option_letter' => 'B',
            'answer_text' => 'False',
            'is_correct' => true,
            'explanation' => 'Correct: Treat Caller-ID as untrusted.'
        ]);

        // ============================================================
        // LESSON 4: Social Media Impersonation & Hijacked Accounts (Prerequisite: Lesson 3)
        // ============================================================
        Lesson::factory()->create([
            'image_path' => '',
            'title' => 'Social Media Impersonation & Hijacked Accounts',
            'description' => 'Learn to identify and respond to social media threats',
            'time' => 15,
            'difficulty' => 'MEDIUM',
            'is_active' => true,
            'prerequisite_lesson_id' => 3, // Requires Lesson 3
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

        // Quiz for Lesson 4
        Quiz::factory()->create([
            'lesson_id' => 4,
            'title' => 'Social Media Impersonation & Hijacked Accounts Quiz',
            'passing_score' => 80,
            'is_active' => true,
        ]);

        // Q1 - Lesson 4
        Question::factory()->create([
            'quiz_id' => 4,
            'question_text' => 'A "friend" asks for e-wallet codes urgently via chat. Best response?',
            'order' => 1,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 16,
            'option_letter' => 'A',
            'answer_text' => 'Send a small amount to be helpful',
            'is_correct' => false,
            'explanation' => 'Incorrect: Any amount can be stolen.'
        ]);
        Answer::factory()->create([
            'question_id' => 16,
            'option_letter' => 'B',
            'answer_text' => 'Verify via a known number and report the chat',
            'is_correct' => true,
            'explanation' => 'Correct: Out-of-band verification defeats impostors; reporting triggers action.'
        ]);
        Answer::factory()->create([
            'question_id' => 16,
            'option_letter' => 'C',
            'answer_text' => 'Keep chatting to gather details',
            'is_correct' => false,
            'explanation' => 'Incorrect: Prolongs exposure.'
        ]);
        Answer::factory()->create([
            'question_id' => 16,
            'option_letter' => 'D',
            'answer_text' => 'Post publicly first',
            'is_correct' => false,
            'explanation' => 'Incorrect: Public posts don\'t verify identity and may leak info.'
        ]);

        // Q2 - Lesson 4
        Question::factory()->create([
            'quiz_id' => 4,
            'question_text' => 'Which is a classic impostor sign?',
            'order' => 2,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 17,
            'option_letter' => 'A',
            'answer_text' => 'Years of visible post history',
            'is_correct' => false,
            'explanation' => 'Incorrect: These suggest legitimacy (still verify).'
        ]);
        Answer::factory()->create([
            'question_id' => 17,
            'option_letter' => 'B',
            'answer_text' => 'Verified badge',
            'is_correct' => false,
            'explanation' => 'Incorrect: These suggest legitimacy (still verify).'
        ]);
        Answer::factory()->create([
            'question_id' => 17,
            'option_letter' => 'C',
            'answer_text' => 'Newly created duplicate profile using your friend\'s photos',
            'is_correct' => true,
            'explanation' => 'Correct: Fresh duplicates with stolen photos are classic red flags.'
        ]);
        Answer::factory()->create([
            'question_id' => 17,
            'option_letter' => 'D',
            'answer_text' => 'Detailed work history',
            'is_correct' => false,
            'explanation' => 'Incorrect: These suggest legitimacy (still verify).'
        ]);

        // Q3 - Lesson 4
        Question::factory()->create([
            'quiz_id' => 4,
            'question_text' => 'If your account is hijacked, first step:',
            'order' => 3,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 18,
            'option_letter' => 'A',
            'answer_text' => 'Delete the app',
            'is_correct' => false,
            'explanation' => 'Incorrect: Don\'t resolve the compromise.'
        ]);
        Answer::factory()->create([
            'question_id' => 18,
            'option_letter' => 'B',
            'answer_text' => 'Change password and end unknown sessions',
            'is_correct' => true,
            'explanation' => 'Correct: Immediately cuts attacker access.'
        ]);
        Answer::factory()->create([
            'question_id' => 18,
            'option_letter' => 'C',
            'answer_text' => 'Create a new profile',
            'is_correct' => false,
            'explanation' => 'Incorrect: Don\'t resolve the compromise.'
        ]);
        Answer::factory()->create([
            'question_id' => 18,
            'option_letter' => 'D',
            'answer_text' => 'Ignore it',
            'is_correct' => false,
            'explanation' => 'Incorrect: Don\'t resolve the compromise.'
        ]);

        // Q4 - Lesson 4
        Question::factory()->create([
            'quiz_id' => 4,
            'question_text' => 'Requesting gift-card or e-wallet codes is common in social-media fraud.',
            'order' => 4,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 19,
            'option_letter' => 'A',
            'answer_text' => 'True',
            'is_correct' => true,
            'explanation' => 'Correct: A well-known pattern in impostor scams.'
        ]);
        Answer::factory()->create([
            'question_id' => 19,
            'option_letter' => 'B',
            'answer_text' => 'False',
            'is_correct' => false,
            'explanation' => 'Incorrect: Evidence shows it\'s common.'
        ]);

        // Q5 - Lesson 4
        Question::factory()->create([
            'quiz_id' => 4,
            'question_text' => 'After reporting a fake profile, warning your contacts is useful.',
            'order' => 5,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 20,
            'option_letter' => 'A',
            'answer_text' => 'True',
            'is_correct' => true,
            'explanation' => 'Correct: Reduces second-wave victimization.'
        ]);
        Answer::factory()->create([
            'question_id' => 20,
            'option_letter' => 'B',
            'answer_text' => 'False',
            'is_correct' => false,
            'explanation' => 'Incorrect: Silence leaves others exposed.'
        ]);

        // ============================================================
        // LESSON 5: Identity Theft & Data Protection (Prerequisite: Lesson 4)
        // ============================================================
        Lesson::factory()->create([
            'image_path' => '',
            'title' => 'Identity Theft & Data Protection',
            'description' => 'Protect your personal information and prevent identity theft',
            'time' => 15,
            'difficulty' => 'HARD',
            'is_active' => true,
            'prerequisite_lesson_id' => 4, // Requires Lesson 4
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

        // Quiz for Lesson 5
        Quiz::factory()->create([
            'lesson_id' => 5,
            'title' => 'Identity Theft & Data Protection Quiz',
            'passing_score' => 80,
            'is_active' => true,
        ]);

        // Q1 - Lesson 5
        Question::factory()->create([
            'quiz_id' => 5,
            'question_text' => 'Sending photos of a national ID through chat to a "staff" account is acceptable if urgent.',
            'order' => 1,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 21,
            'option_letter' => 'A',
            'answer_text' => 'True',
            'is_correct' => false,
            'explanation' => 'Incorrect: Urgency is a manipulation tactic; chat isn\'t an approved secure channel.'
        ]);
        Answer::factory()->create([
            'question_id' => 21,
            'option_letter' => 'B',
            'answer_text' => 'False',
            'is_correct' => true,
            'explanation' => 'Correct: Use approved portals for sensitive documents.'
        ]);

        // Q2 - Lesson 5
        Question::factory()->create([
            'quiz_id' => 5,
            'question_text' => '"Minimal-PII" means:',
            'order' => 2,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 22,
            'option_letter' => 'A',
            'answer_text' => 'Collect everything just in case',
            'is_correct' => false,
            'explanation' => 'Incorrect: Over-collection and casual sharing increase risk.'
        ]);
        Answer::factory()->create([
            'question_id' => 22,
            'option_letter' => 'B',
            'answer_text' => 'Share only what\'s needed for the task',
            'is_correct' => true,
            'explanation' => 'Correct: Reduces exposure and impact if data leaks.'
        ]);
        Answer::factory()->create([
            'question_id' => 22,
            'option_letter' => 'C',
            'answer_text' => 'Keep copies on personal drives',
            'is_correct' => false,
            'explanation' => 'Incorrect: Over-collection and casual sharing increase risk.'
        ]);
        Answer::factory()->create([
            'question_id' => 22,
            'option_letter' => 'D',
            'answer_text' => 'Forward all documents to group chats',
            'is_correct' => false,
            'explanation' => 'Incorrect: Over-collection and casual sharing increase risk.'
        ]);

        // Q3 - Lesson 5
        Question::factory()->create([
            'quiz_id' => 5,
            'question_text' => 'After suspected exposure, best first move:',
            'order' => 3,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 23,
            'option_letter' => 'A',
            'answer_text' => 'Wait to see what happens',
            'is_correct' => false,
            'explanation' => 'Incorrect: Delay/inaction increases risk.'
        ]);
        Answer::factory()->create([
            'question_id' => 23,
            'option_letter' => 'B',
            'answer_text' => 'Change passwords and enable MFA',
            'is_correct' => true,
            'explanation' => 'Correct: Immediately limits further misuse.'
        ]);
        Answer::factory()->create([
            'question_id' => 23,
            'option_letter' => 'C',
            'answer_text' => 'Post about it online',
            'is_correct' => false,
            'explanation' => 'Incorrect: Public posts don\'t secure accounts.'
        ]);
        Answer::factory()->create([
            'question_id' => 23,
            'option_letter' => 'D',
            'answer_text' => 'Do nothing',
            'is_correct' => false,
            'explanation' => 'Incorrect: Delay/inaction increases risk.'
        ]);

        // Q4 - Lesson 5
        Question::factory()->create([
            'quiz_id' => 5,
            'question_text' => 'Safest channel for submitting official documents:',
            'order' => 4,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 24,
            'option_letter' => 'A',
            'answer_text' => 'Personal email',
            'is_correct' => false,
            'explanation' => 'Incorrect: Lack controls/audit; may expose data.'
        ]);
        Answer::factory()->create([
            'question_id' => 24,
            'option_letter' => 'B',
            'answer_text' => 'Public cloud folder',
            'is_correct' => false,
            'explanation' => 'Incorrect: Lack controls/audit; may expose data.'
        ]);
        Answer::factory()->create([
            'question_id' => 24,
            'option_letter' => 'C',
            'answer_text' => 'Unverified web form',
            'is_correct' => false,
            'explanation' => 'Incorrect: Lack controls/audit; may expose data.'
        ]);
        Answer::factory()->create([
            'question_id' => 24,
            'option_letter' => 'D',
            'answer_text' => 'Approved campus portal',
            'is_correct' => true,
            'explanation' => 'Correct: Institutional portals enforce security and logging.'
        ]);

        // Q5 - Lesson 5
        Question::factory()->create([
            'quiz_id' => 5,
            'question_text' => 'Stolen data is often reused to make later scams more convincing.',
            'order' => 5,
            'points' => 1,
        ]);
        Answer::factory()->create([
            'question_id' => 25,
            'option_letter' => 'A',
            'answer_text' => 'True',
            'is_correct' => true,
            'explanation' => 'Correct: Reuse of compromised data is common in follow-on attacks.'
        ]);
        Answer::factory()->create([
            'question_id' => 25,
            'option_letter' => 'B',
            'answer_text' => 'False',
            'is_correct' => false,
            'explanation' => 'Incorrect: Evidence shows repeated reuse.'
        ]);
    }
}
