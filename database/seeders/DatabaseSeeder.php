<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\SimulationAttempt;
use App\Models\StudentLesson;
use App\Models\User;
use App\Models\UserQuizAttempt;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private $questionCounter = 1;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->createUsers();
        $this->createLessonsAndQuizzes();

        $students = $this->createStudents(50);
        
        // Get all active lessons
        $lessons = Lesson::where('is_active', true)->get();
        
        // Create realistic progress for each student
        foreach ($students as $student) {
            $this->createStudentProgress($student, $lessons);
        }
    }

    private function createUsers(): void
    {
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
    }

        private function createLessonsAndQuizzes(): void
    {
        // Lesson 1
        $this->createLesson(
            null,
            'Introduction to Cybersecurity',
            'An introductory lesson for cybersecurity fundamentals',
            15,
            'EASY',
            $this->getLesson1Content(),
            'Introduction to Cybersecurity Quiz',
            80,
            $this->getLesson1Questions(),
            true
        );

        // Lesson 2
        $this->createLesson(
            1,
            'Social Engineering Fundamentals',
            'Learn about social engineering tactics and how to defend against them',
            15,
            'EASY',
            $this->getLesson2Content(),
            'Social Engineering Fundamentals Quiz',
            80,
            $this->getLesson2Questions(),
            true
        );

        // Lesson 3
        $this->createLesson(
            2,
            'Phishing, Smishing, and Vishing',
            'Understand different types of phishing attacks and how to identify them',
            15,
            'MEDIUM',
            $this->getLesson3Content(),
            'Phishing, Smishing, and Vishing Quiz',
            80,
            $this->getLesson3Questions(),
            true
        );

        // Lesson 4
        $this->createLesson(
            3,
            'Social Media Impersonation & Hijacked Accounts',
            'Learn to identify and respond to social media threats',
            15,
            'MEDIUM',
            $this->getLesson4Content(),
            'Social Media Impersonation & Hijacked Accounts Quiz',
            80,
            $this->getLesson4Questions()
        );

        // Lesson 5
        $this->createLesson(
            4,
            'Identity Theft & Data Protection',
            'Protect your personal information and prevent identity theft',
            15,
            'HARD',
            $this->getLesson5Content(),
            'Identity Theft & Data Protection Quiz',
            80,
            $this->getLesson5Questions()
        );

        // Lesson 6
        $this->createLesson(
            5,
            'AI-Enhanced Scams and Deepfakes',
            'Understand AI-driven threats and deepfake attacks',
            15,
            'HARD',
            $this->getLesson6Content(),
            'AI-Enhanced Scams and Deepfakes Quiz',
            80,
            $this->getLesson6Questions()
        );

        // Lesson 7
        $this->createLesson(
            6,
            'Pretexting, Baiting, and Advanced Manipulation',
            'Learn about advanced social engineering techniques',
            15,
            'HARD',
            $this->getLesson7Content(),
            'Pretexting, Baiting, and Advanced Manipulation Quiz',
            80,
            $this->getLesson7Questions()
        );

        // Lesson 8
        $this->createLesson(
            7,
            'Cybercrime Laws in the Philippines (RA 10175)',
            'Understand Philippine cybercrime laws and regulations',
            15,
            'MEDIUM',
            $this->getLesson8Content(),
            'Cybercrime Laws (RA 10175) Quiz',
            80,
            $this->getLesson8Questions()
        );

        // Lesson 9
        $this->createLesson(
            8,
            'Reporting and Enforcement Mechanisms',
            'Learn how to report cybercrimes and understand enforcement processes',
            15,
            'MEDIUM',
            $this->getLesson9Content(),
            'Reporting and Enforcement Mechanisms Quiz',
            80,
            $this->getLesson9Questions()
        );

        // Lesson 10
        $this->createLesson(
            9,
            'Cyber Hygiene & Personal Defense Strategies',
            'Develop strong cyber hygiene habits and personal defense strategies',
            15,
            'EASY',
            $this->getLesson10Content(),
            'Cyber Hygiene & Personal Defense Strategies Quiz',
            80,
            $this->getLesson10Questions()
        );

        // Lesson 11
        $this->createLesson(
            10,
            'Organizational & Workplace Security',
            'Understand security practices in organizational settings',
            15,
            'MEDIUM',
            $this->getLesson11Content(),
            'Organizational & Workplace Security Quiz',
            80,
            $this->getLesson11Questions()
        );

        // Lesson 12
        $this->createLesson(
            11,
            'Emerging Threats & Future Trends',
            'Explore emerging cyber threats and future security trends',
            15,
            'HARD',
            $this->getLesson12Content(),
            'Emerging Threats & Future Trends Quiz',
            80,
            $this->getLesson12Questions()
        );

        // Lesson 13
        $this->createLesson(
            12,
            'Strategic Cybersecurity Planning & National Readiness',
            'Understand strategic planning and national cybersecurity readiness',
            15,
            'HARD',
            $this->getLesson13Content(),
            'Strategic Cybersecurity Planning & National Readiness Quiz',
            80,
            $this->getLesson13Questions()
        );
    }

    private function createLesson($prerequisiteId, $title, $description, $time, $difficulty, $content, $quizTitle, $passingScore, $questions, $hasSimulation=false): void
    {
        $lesson = Lesson::factory()->create([
            'image_path' => '',
            'title' => $title,
            'description' => $description,
            'time' => $time,
            'difficulty' => $difficulty,
            'is_active' => true,
            'prerequisite_lesson_id' => $prerequisiteId,
            'content' => $content,
            'has_simulation' => $hasSimulation,
        ]);

        $quiz = Quiz::factory()->create([
            'lesson_id' => $lesson->id,
            'title' => $quizTitle,
            'passing_score' => $passingScore,
            'is_active' => true,
        ]);

        $this->createQuestions($quiz->id, $questions);
    }

    private function createQuestions($quizId, $questions): void
    {
        foreach ($questions as $index => $questionData) {
            $question = Question::factory()->create([
                'quiz_id' => $quizId,
                'question_text' => $questionData['question'],
                'order' => $index + 1,
                'points' => 1,
            ]);

            foreach ($questionData['answers'] as $answerIndex => $answer) {
                Answer::factory()->create([
                    'question_id' => $this->questionCounter,
                    'option_letter' => chr(65 + $answerIndex),
                    'answer_text' => $answer['text'],
                    'is_correct' => $answer['correct'],
                    'explanation' => $answer['explanation']
                ]);
            }

            $this->questionCounter++;
        }
    }

    // ============================================================
    // LESSON CONTENT METHODS
    // ============================================================

    private function getLesson1Content(): string
    {
        return "
            <h4>Introduction</h4>
            <p>
                Cybersecurity is the practice of protecting systems, networks, applications, and data from unauthorized access, misuse, disruption, or destruction. In a university or organizational environment, this includes learning management systems, email accounts, grading portals, research databases, cloud storage, and personal devices used for work or study.
            </p>
            <p>
                The primary goal of cybersecurity is to ensure that digital information and systems remain protected from harm. This protection is guided by three core principles known as the CIA Triad: Confidentiality, Integrity, and Availability.
            </p>
            <h4>The CIA Triad</h4>
            <p><strong>1. Confidentiality</strong><br>
            Confidentiality ensures that information is only accessible to authorized individuals. Examples include using strong passwords, enabling multi-factor authentication (MFA), locking your screen when away from your device, and avoiding sharing login credentials. A loss of confidentiality occurs when private data is viewed or accessed by someone without permission.</p>
            
            <p><strong>2. Integrity</strong><br>
            Integrity ensures that information remains accurate, complete, and unaltered unless changed by an authorized user. Cybersecurity measures that support integrity include access controls, version tracking, and system logs. An integrity violation occurs when data is modified, tampered with, or altered without authorization.</p>
            
            <p><strong>3. Availability</strong><br>
            Availability ensures that systems and data are accessible when needed. This is supported by regular system maintenance, software updates, backups, and protection against attacks such as ransomware or denial-of-service incidents. Loss of availability may result in system downtime or inability to access important data.</p>
            
            <h4>Cybersecurity: People, Process, and Technology</h4>
            <p>
                Cybersecurity is not only about technical tools. It is a combination of people, processes, and technology working together. Even with advanced security software, many incidents still occur because attackers exploit human behavior.
            </p>
            <p>
                Common human-related risks include clicking on suspicious links, acting quickly on urgent messages, reusing passwords, or failing to log out of shared computers. Developing safe habits such as pausing before acting, verifying information, and reporting suspicious activity significantly reduces cybersecurity risk.
            </p>
            <h4>Best Practices</h4>
            <ul>
                <li>Use strong, unique passwords and enable MFA</li>
                <li>Keep operating systems, browsers, and apps updated</li>
                <li>Lock shared/lab devices and sign out when finished</li>
                <li>Treat unexpected links/attachments cautiously; verify first</li>
                <li>Report anything suspicious through the official channel</li>
            </ul>
        ";
    }

    private function getLesson2Content(): string
    {
        return "
            <h4>Introduction</h4>
            <p>
                Social engineering is a category of cyberattack that focuses on manipulating people rather than exploiting technical vulnerabilities. Attackers use deception, trust, fear, urgency, or authority to persuade individuals to perform unsafe actions such as clicking malicious links, sharing passwords or one-time passwords (OTPs), or sending money.
            </p>
            <p>
                Because humans are naturally helpful and responsive to authority and urgency, social engineering remains one of the most effective attack methods. Understanding how these attacks work is essential for preventing security incidents.
            </p>
            <h4>Common Psychological Triggers</h4>
            <ul>
                <li><strong>Authority:</strong> Pretending to be IT staff, management, banks, or government offices</li>
                <li><strong>Urgency:</strong> Creating pressure with deadlines or threats</li>
                <li><strong>Fear:</strong> Warning of penalties, account suspension, or legal trouble</li>
                <li><strong>Scarcity:</strong> Claiming limited time or availability</li>
                <li><strong>Reciprocity:</strong> Offering rewards or help in exchange for compliance</li>
            </ul>
            <h4>Safe Response Model: Pause, Verify, Report</h4>
            <p>The safest response to any suspicious or unexpected message is to:</p>
            <ul>
                <li><strong>Pause:</strong> Do not click, reply, or act immediately</li>
                <li><strong>Verify:</strong> Confirm the request using a trusted, official channel</li>
                <li><strong>Report:</strong> Notify the appropriate security or IT team</li>
            </ul>
        ";
    }

    private function getLesson3Content(): string
    {
        return "
            <h4>Introduction</h4>
            <p>
                Phishing, smishing, and vishing are social engineering attacks delivered through different communication channels. While the delivery methods vary, the goal is the same: to trick individuals into revealing sensitive information, clicking malicious links, or sending money.
            </p>
            <p>
                Understanding how these attack types differ, their common warning signs, and the correct response steps is essential for preventing account compromise and financial loss.
            </p>
            <h4>Types of Attacks</h4>
            <ul>
                <li><strong>Phishing:</strong> Delivered through email using fake login pages, malicious attachments, or impersonated organizations</li>
                <li><strong>Smishing:</strong> Delivered through SMS or messaging apps with urgent payment requests or suspicious links</li>
                <li><strong>Vishing:</strong> Delivered through voice calls where attackers impersonate banks, IT support, or government offices</li>
            </ul>
            <h4>Common Warning Signs</h4>
            <ul>
                <li>Unexpected messages asking you to act quickly</li>
                <li>Requests for passwords, OTPs, PINs, or recovery codes</li>
                <li>Suspicious or shortened links</li>
                <li>Caller pressure or refusal to let you verify independently</li>
                <li>Messages that create fear of penalties or account suspension</li>
            </ul>
            <h4>Safe Response Procedure</h4>
            <ul>
                <li>Pause and do not interact</li>
                <li>Preserve evidence such as screenshots or call details</li>
                <li>Verify the request using official apps, websites, or hotlines</li>
                <li>Report the incident through the proper channel</li>
            </ul>
        ";
    }

    private function getLesson4Content(): string
    {
        return "
            <h4>Introduction</h4>
            <p>
                Social media platforms are frequently abused by attackers to impersonate real people or to take control of legitimate accounts. These attacks rely on familiarity and trust, making them especially effective against friends, classmates, and coworkers.
            </p>
            <p>
                Impersonation and account hijacking can lead to financial loss, data exposure, and damage to personal or institutional reputation. Understanding how these attacks work and how to respond quickly is critical to limiting their impact.
            </p>
            <h4>Impersonation vs. Hijacked Accounts</h4>
            <p>
                <strong>Impersonation</strong> occurs when an attacker creates a fake profile using someone else's name, photos, or identity. These profiles are often newly created and have limited history.
            </p>
            <p>
                <strong>Account hijacking</strong> occurs when an attacker gains access to a real account, often through phishing or password reuse. Hijacked accounts may appear legitimate but suddenly behave unusually.
            </p>
            <h4>Common Warning Signs</h4>
            <ul>
                <li>Duplicate or newly created profiles using familiar names or photos</li>
                <li>Sudden private messages asking for money, gift cards, or codes</li>
                <li>Messages that create urgency or secrecy</li>
                <li>Posts or messages that do not match the person's normal behavior</li>
                <li>Requests to move conversations off-platform</li>
            </ul>
        ";
    }

    private function getLesson5Content(): string
    {
        return "
            <h4>Introduction</h4>
            <p>
                Identity theft occurs when someone steals and misuses another person's personal or sensitive information without permission. This information can include names, addresses, government-issued ID numbers, account credentials, photos of IDs, and financial details.
            </p>
            <p>
                Once personal data is exposed, attackers often reuse it to conduct additional scams, commit fraud, or impersonate victims. Understanding how identity theft happens and how to protect personal data is essential for reducing long-term risk.
            </p>
            <h4>Types of Personal and Sensitive Data</h4>
            <p>
                <strong>Personal data</strong> includes information that can identify an individual, such as full name, address, phone number, and email address.
            </p>
            <p>
                <strong>Sensitive data</strong> includes information that can cause serious harm if exposed, such as government ID numbers, passwords, OTPs, financial account numbers, and biometric data.
            </p>
            <h4>Best Practices for Data Protection</h4>
            <ul>
                <li>Share only the minimum personal information required for a task</li>
                <li>Use approved or official portals for submitting documents</li>
                <li>Avoid sending IDs or sensitive data through email or chat apps</li>
                <li>Redact sensitive details when full information is not required</li>
                <li>Use strong, unique passwords and enable multi-factor authentication (MFA)</li>
            </ul>
        ";
    }

    private function getLesson6Content(): string
    {
        return "
            <h4>Introduction</h4>
            <p>
                Artificial intelligence (AI) is increasingly used in scams to make messages, voices, and videos look and sound more realistic. These AI-enhanced scams reduce the obvious warning signs that older scams often had (poor grammar, inconsistent formatting), which increases the chance that people will trust the message and act quickly.
            </p>
            <p>
                A deepfake is AI-generated or AI-altered media that imitates a real person's face, voice, or behavior. Deepfakes can be used to impersonate executives, managers, instructors, family members, or customer support agents. Because seeing or hearing is no longer reliable proof, verification must be based on trusted processes—not appearances.
            </p>
            <h4>How AI Enhances Scams</h4>
            <ul>
                <li>Polished writing: generating professional emails and chat messages that look legitimate</li>
                <li>Automation at scale: sending tailored messages to many people quickly</li>
                <li>Personalization: using public data to craft believable stories</li>
                <li>Conversation simulation: chatbots that behave like real support agents</li>
            </ul>
            <h4>Defending Against Deepfakes and AI Scams</h4>
            <p>Use a process-based defense:</p>
            <ol>
                <li>Pause: do not act immediately</li>
                <li>Verify independently: use an official directory, known hotline, official app, or a saved contact</li>
                <li>Use call-back procedures: hang up and call the known number yourself</li>
                <li>Use pre-agreed verification: shared passphrases, code words, or internal approval steps</li>
                <li>Preserve evidence: screenshots, sender details, call logs</li>
                <li>Report quickly: early reporting helps stop repeated targeting</li>
            </ol>
        ";
    }

    private function getLesson7Content(): string
    {
        return "
            <h4>Introduction</h4>
            <p>
                Pretexting and baiting are advanced social engineering techniques that rely on carefully constructed stories and psychological manipulation. Unlike simple scams, these attacks often involve preparation, research, and believable scenarios designed to lower a victim's guard.
            </p>
            <p>
                Attackers using these techniques may spend time gathering information about their targets to make interactions feel legitimate. Understanding how these methods work helps individuals recognize manipulation before harm occurs.
            </p>
            <h4>Pretexting</h4>
            <p>
                Pretexting involves creating a false but believable story (the pretext) to gain trust. Attackers may impersonate IT staff, auditors, delivery personnel, bank employees, or school officials. The goal is to convince the victim that the request is legitimate.
            </p>
            <h4>Baiting</h4>
            <p>
                Baiting exploits curiosity or greed by offering something attractive, such as free software, USB drives, prizes, or access to exclusive content. Once the victim interacts with the bait, malware may be installed or information may be stolen.
            </p>
            <h4>Advanced Manipulation Techniques</h4>
            <ul>
                <li>Authority: pretending to be someone in power</li>
                <li>Familiarity: referencing known people or events</li>
                <li>Consistency: starting with small requests that escalate</li>
                <li>Scarcity: claiming limited time or opportunity</li>
                <li>Fear: threatening consequences for non-compliance</li>
            </ul>
        ";
    }

    private function getLesson8Content(): string
    {
        return "
            <h4>Introduction</h4>
            <p>
                The Cybercrime Prevention Act of 2012, officially known as Republic Act No. 10175 (RA 10175), is the primary law in the Philippines that defines, prevents, and penalizes cybercrime. This law was enacted to address crimes committed through computers, mobile devices, networks, and the internet.
            </p>
            <p>
                Understanding RA 10175 is important for students, employees, and professionals because many everyday online actions—if done improperly—can result in legal consequences.
            </p>
            <h4>Categories of Cybercrime Under RA 10175</h4>
            <ol>
                <li><strong>Offenses against the confidentiality, integrity, and availability of computer data and systems</strong><br>
                Examples: illegal access (hacking), data interference, system interference</li>
                <li><strong>Computer-related offenses</strong><br>
                Examples: computer-related fraud, identity theft, forgery</li>
                <li><strong>Content-related offenses</strong><br>
                Examples: cyber libel, child pornography, online sexual exploitation</li>
            </ol>
            <h4>Key Provisions</h4>
            <ul>
                <li>Illegal access: intentionally accessing a computer system without authority</li>
                <li>Cyber libel: libelous acts committed through a computer system</li>
                <li>Identity theft: unauthorized use of another person's identifying information</li>
                <li>Online fraud: scams, phishing, and deceptive activities using computer systems</li>
            </ul>
        ";
    }

    private function getLesson9Content(): string
    {
        return "
            <h4>Introduction</h4>
            <p>
                Reporting and enforcement mechanisms are essential components of effective cybersecurity. Even the best security controls cannot fully prevent incidents, so timely reporting and proper enforcement actions are critical to limiting damage, identifying offenders, and protecting others.
            </p>
            <p>
                In the Philippines, cybercrime reporting involves coordination between individuals, institutions, service providers, and law enforcement agencies. Understanding how and when to report incidents helps ensure that cases are handled correctly and lawfully.
            </p>
            <h4>Why Reporting Matters</h4>
            <ul>
                <li>Stop ongoing attacks and prevent repeat victims</li>
                <li>Preserve digital evidence before it is lost</li>
                <li>Enable authorities to investigate and prosecute offenders</li>
                <li>Improve organizational defenses through awareness of attack patterns</li>
            </ul>
            <h4>Who to Report To</h4>
            <ul>
                <li>Internal IT or security teams (for school or workplace incidents)</li>
                <li>Platform or service providers (email, social media, banks)</li>
                <li>Law enforcement: Philippine National Police Anti-Cybercrime Group (PNP-ACG)</li>
                <li>National Bureau of Investigation Cybercrime Division (NBI-CCD)</li>
            </ul>
        ";
    }

    private function getLesson10Content(): string
    {
        return "
            <h4>Introduction</h4>
            <p>
                Cyber hygiene refers to the routine practices and habits that individuals follow to maintain the security and health of their digital lives. Just as personal hygiene prevents illness, good cyber hygiene reduces the risk of cyberattacks, data breaches, and identity theft.
            </p>
            <p>
                Strong personal defense strategies focus on prevention first. Most cyber incidents succeed not because of advanced hacking, but because basic security practices were ignored or overlooked.
            </p>
            <h4>Strong Password Practices</h4>
            <ul>
                <li>Using long, unique passwords for every account</li>
                <li>Avoiding personal information (names, birthdays)</li>
                <li>Never reusing passwords across multiple services</li>
                <li>Using password managers to store credentials securely</li>
            </ul>
            <h4>Multi-Factor Authentication (MFA)</h4>
            <p>
                Multi-factor authentication adds an extra layer of security by requiring more than one form of verification. Even if a password is stolen, MFA can prevent attackers from accessing accounts.
            </p>
            <h4>Device and Software Security</h4>
            <ul>
                <li>Installing updates and security patches promptly</li>
                <li>Using antivirus or endpoint protection software</li>
                <li>Locking devices with PINs, passwords, or biometrics</li>
                <li>Avoiding installation of untrusted software or apps</li>
            </ul>
        ";
    }

    private function getLesson11Content(): string
    {
        return "
            <h4>Introduction</h4>
            <p>
                Organizational and workplace security focuses on protecting systems, data, and people within an institution such as a company, school, or government office. While technology plays an important role, employee behavior and adherence to policies are equally critical.
            </p>
            <p>
                Many security incidents in workplaces occur due to policy violations, poor access control, or failure to follow established procedures. Understanding workplace security principles helps reduce both internal and external threats.
            </p>
            <h4>Security Policies and Acceptable Use</h4>
            <p>
                Organizations establish security policies to define acceptable and unacceptable behavior. These policies typically cover device usage, internet access, data handling, and reporting requirements. Employees are expected to understand and follow these rules at all times.
            </p>
            <h4>Access Control and Least Privilege</h4>
            <p>
                Access control ensures that employees can only access systems and data required for their role. The principle of least privilege limits damage if an account is compromised by reducing unnecessary access.
            </p>
            <h4>Physical Security in the Workplace</h4>
            <ul>
                <li>ID badges and access cards</li>
                <li>Locked doors and server rooms</li>
                <li>Visitor sign-in procedures</li>
                <li>Clear desk and screen-lock policies</li>
            </ul>
        ";
    }

    private function getLesson12Content(): string
    {
        return "
            <h4>Introduction</h4>
            <p>
                Cyber threats continuously evolve as technology advances. Emerging threats take advantage of new tools, platforms, and behaviors, often spreading faster than traditional security controls can adapt. Understanding future trends helps individuals and organizations prepare proactively rather than reacting after damage occurs.
            </p>
            <h4>Automation and AI-Driven Attacks</h4>
            <p>
                Automation and artificial intelligence enable attackers to launch large-scale, highly personalized attacks with minimal effort. AI can generate convincing messages, analyze targets quickly, and adapt attacks in real time based on responses.
            </p>
            <h4>Expansion of Attack Surfaces</h4>
            <p>
                As more devices and services connect to the internet, the attack surface expands. Cloud platforms, mobile devices, smart devices (IoT), and remote work environments introduce new entry points that attackers can exploit.
            </p>
            <h4>Supply Chain and Third-Party Risks</h4>
            <p>
                Organizations increasingly rely on third-party services and vendors. A security weakness in one provider can expose many connected organizations, making supply chain attacks especially damaging and difficult to detect.
            </p>
            <h4>Data-Centric Attacks and Privacy Risks</h4>
            <p>
                Modern cybercrime often targets data rather than systems. Stolen personal data, credentials, and intellectual property are reused for fraud, identity theft, and long-term exploitation.
            </p>
        ";
    }

    private function getLesson13Content(): string
    {
        return "
            <h4>Introduction</h4>
            <p>
                Strategic cybersecurity planning focuses on long-term, coordinated efforts to protect critical systems, data, and national interests from cyber threats. Unlike daily security operations, strategic planning anticipates risks, allocates resources, and builds resilience.
            </p>
            <p>
                National cybersecurity readiness ensures governments, institutions, and citizens can prevent, respond to, and recover from cyber incidents that affect public safety, the economy, and national security.
            </p>
            <h4>Strategic Cybersecurity Planning</h4>
            <p>
                Strategic planning defines long-term cybersecurity goals, policies, and frameworks. It includes risk assessment, protection of critical assets, investment in capabilities, and continuous evaluation of emerging threats.
            </p>
            <h4>National Cybersecurity Readiness</h4>
            <p>
                National readiness refers to a country's capacity to defend against large-scale cyber incidents impacting critical infrastructure such as energy, healthcare, finance, transport, and government services.
            </p>
            <h4>Roles and Stakeholders</h4>
            <ul>
                <li>Government and policymakers</li>
                <li>Law enforcement and national CERT/CSIRT teams</li>
                <li>Private sector and critical infrastructure operators</li>
                <li>Educational institutions and citizens</li>
            </ul>
            <h4>International Cooperation</h4>
            <p>
                International cooperation enables threat intelligence sharing, coordinated responses, and development of norms for responsible behavior in cyberspace.
            </p>
        ";
    }

    // ============================================================
    // QUIZ QUESTIONS METHODS
    // ============================================================

    private function getLesson1Questions(): array
    {
        return [
            [
                'question' => 'What is the primary goal of cybersecurity?',
                'answers' => [
                    ['text' => 'Increase internet speed', 'correct' => false, 'explanation' => 'Incorrect: Not the primary goal.'],
                    ['text' => 'Protect systems and data from harm', 'correct' => true, 'explanation' => 'Correct: The lesson defines cybersecurity as protecting systems and data.'],
                    ['text' => 'Monitor user behavior', 'correct' => false, 'explanation' => 'Incorrect: Monitoring is a component, not the primary goal.'],
                    ['text' => 'Reduce hardware costs', 'correct' => false, 'explanation' => 'Incorrect: Not related to cybersecurity.'],
                ]
            ],
            [
                'question' => 'Which CIA triad principle focuses on keeping information secret?',
                'answers' => [
                    ['text' => 'Integrity', 'correct' => false, 'explanation' => 'Incorrect: Integrity is about accuracy.'],
                    ['text' => 'Availability', 'correct' => false, 'explanation' => 'Incorrect: Availability is about access.'],
                    ['text' => 'Confidentiality', 'correct' => true, 'explanation' => 'Correct: Confidentiality limits access to authorized individuals.'],
                    ['text' => 'Authorization', 'correct' => false, 'explanation' => 'Incorrect: Not part of CIA triad.'],
                ]
            ],
            [
                'question' => 'Integrity in cybersecurity ensures that data is:',
                'answers' => [
                    ['text' => 'Always accessible', 'correct' => false, 'explanation' => 'Incorrect: This is availability.'],
                    ['text' => 'Encrypted', 'correct' => false, 'explanation' => 'Incorrect: Encryption supports confidentiality.'],
                    ['text' => 'Accurate and unaltered', 'correct' => true, 'explanation' => 'Correct: Integrity protects data from unauthorized modification.'],
                    ['text' => 'Frequently backed up', 'correct' => false, 'explanation' => 'Incorrect: Backups support availability.'],
                ]
            ],
            [
                'question' => 'Availability ensures that systems are:',
                'answers' => [
                    ['text' => 'Hidden from users', 'correct' => false, 'explanation' => 'Incorrect: Opposite of availability.'],
                    ['text' => 'Accessible when needed', 'correct' => true, 'explanation' => 'Correct: Availability focuses on uptime and access.'],
                    ['text' => 'Password-free', 'correct' => false, 'explanation' => 'Incorrect: Security requires authentication.'],
                    ['text' => 'Offline', 'correct' => false, 'explanation' => 'Incorrect: Offline means unavailable.'],
                ]
            ],
            [
                'question' => 'Which is an example of a confidentiality breach?',
                'answers' => [
                    ['text' => 'System downtime', 'correct' => false, 'explanation' => 'Incorrect: This is availability breach.'],
                    ['text' => 'Data deletion', 'correct' => false, 'explanation' => 'Incorrect: This affects integrity and availability.'],
                    ['text' => 'Unauthorized viewing of private data', 'correct' => true, 'explanation' => 'Correct: Viewing data without permission violates confidentiality.'],
                    ['text' => 'Power outage', 'correct' => false, 'explanation' => 'Incorrect: This affects availability.'],
                ]
            ],
            [
                'question' => 'Why is logging out of shared computers important?',
                'answers' => [
                    ['text' => 'To save electricity', 'correct' => false, 'explanation' => 'Incorrect: Not the security reason.'],
                    ['text' => 'To prevent unauthorized account access', 'correct' => true, 'explanation' => 'Correct: Logging out prevents misuse of your session.'],
                    ['text' => 'To speed up the system', 'correct' => false, 'explanation' => 'Incorrect: Not the primary reason.'],
                    ['text' => 'To enable updates', 'correct' => false, 'explanation' => 'Incorrect: Updates work independently.'],
                ]
            ],
            [
                'question' => 'What does MFA stand for?',
                'answers' => [
                    ['text' => 'Manual Firewall Access', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'Multi-Factor Authentication', 'correct' => true, 'explanation' => 'Correct: MFA requires multiple verification factors.'],
                    ['text' => 'Managed File Authorization', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'Multi-Form Antivirus', 'correct' => false, 'explanation' => 'Incorrect.'],
                ]
            ],
            [
                'question' => 'Which practice best supports confidentiality?',
                'answers' => [
                    ['text' => 'Reusing passwords', 'correct' => false, 'explanation' => 'Incorrect: Weakens security.'],
                    ['text' => 'Locking your screen when away', 'correct' => true, 'explanation' => 'Correct: Screen locking prevents unauthorized viewing.'],
                    ['text' => 'Ignoring updates', 'correct' => false, 'explanation' => 'Incorrect: Updates are important.'],
                    ['text' => 'Sharing credentials', 'correct' => false, 'explanation' => 'Incorrect: Violates confidentiality.'],
                ]
            ],
            [
                'question' => 'What should you do when receiving an unexpected message with a link?',
                'answers' => [
                    ['text' => 'Click immediately', 'correct' => false, 'explanation' => 'Incorrect: May lead to compromise.'],
                    ['text' => 'Reply right away', 'correct' => false, 'explanation' => 'Incorrect: May confirm your contact.'],
                    ['text' => 'Pause and verify the sender', 'correct' => true, 'explanation' => 'Correct: Verification reduces phishing risk.'],
                    ['text' => 'Forward it', 'correct' => false, 'explanation' => 'Incorrect: May spread the threat.'],
                ]
            ],
            [
                'question' => 'Which item is NOT part of the CIA triad?',
                'answers' => [
                    ['text' => 'Confidentiality', 'correct' => false, 'explanation' => 'Incorrect: Part of CIA.'],
                    ['text' => 'Integrity', 'correct' => false, 'explanation' => 'Incorrect: Part of CIA.'],
                    ['text' => 'Availability', 'correct' => false, 'explanation' => 'Incorrect: Part of CIA.'],
                    ['text' => 'Authorization', 'correct' => true, 'explanation' => 'Correct: Authorization is not part of the CIA triad.'],
                ]
            ],
            [
                'question' => 'Why are software updates important?',
                'answers' => [
                    ['text' => 'Add features', 'correct' => false, 'explanation' => 'Incorrect: Secondary benefit.'],
                    ['text' => 'Remove passwords', 'correct' => false, 'explanation' => 'Incorrect: Updates don\'t remove passwords.'],
                    ['text' => 'Patch security vulnerabilities', 'correct' => true, 'explanation' => 'Correct: Updates fix known weaknesses.'],
                    ['text' => 'Slow systems', 'correct' => false, 'explanation' => 'Incorrect: Updates improve security.'],
                ]
            ],
            [
                'question' => 'Which password practice is safest?',
                'answers' => [
                    ['text' => 'Short passwords', 'correct' => false, 'explanation' => 'Incorrect: Weak security.'],
                    ['text' => 'Reused passwords', 'correct' => false, 'explanation' => 'Incorrect: Increases risk.'],
                    ['text' => 'Unique and complex passwords', 'correct' => true, 'explanation' => 'Correct: Strong, unique passwords reduce risk.'],
                    ['text' => 'Writing passwords down', 'correct' => false, 'explanation' => 'Incorrect: Physical exposure risk.'],
                ]
            ],
            [
                'question' => 'Which action best supports availability?',
                'answers' => [
                    ['text' => 'Regular backups', 'correct' => true, 'explanation' => 'Correct: Backups help restore access.'],
                    ['text' => 'Sharing admin access', 'correct' => false, 'explanation' => 'Incorrect: Security risk.'],
                    ['text' => 'Ignoring alerts', 'correct' => false, 'explanation' => 'Incorrect: May lead to downtime.'],
                    ['text' => 'Disabling servers', 'correct' => false, 'explanation' => 'Incorrect: Reduces availability.'],
                ]
            ],
            [
                'question' => 'Many cyber incidents begin because users:',
                'answers' => [
                    ['text' => 'Install updates', 'correct' => false, 'explanation' => 'Incorrect: Updates improve security.'],
                    ['text' => 'Are tricked into acting quickly', 'correct' => true, 'explanation' => 'Correct: Social engineering exploits urgency.'],
                    ['text' => 'Use antivirus', 'correct' => false, 'explanation' => 'Incorrect: Antivirus helps prevent incidents.'],
                    ['text' => 'Follow policy', 'correct' => false, 'explanation' => 'Incorrect: Policies reduce risk.'],
                ]
            ],
            [
                'question' => 'Which is a technical security control?',
                'answers' => [
                    ['text' => 'Policies', 'correct' => false, 'explanation' => 'Incorrect: Administrative control.'],
                    ['text' => 'Awareness training', 'correct' => false, 'explanation' => 'Incorrect: Administrative control.'],
                    ['text' => 'Antivirus software', 'correct' => true, 'explanation' => 'Correct: Antivirus is a technical tool.'],
                    ['text' => 'Peer reminders', 'correct' => false, 'explanation' => 'Incorrect: Behavioral control.'],
                ]
            ],
            [
                'question' => 'What should you do if you suspect your account is compromised?',
                'answers' => [
                    ['text' => 'Ignore it', 'correct' => false, 'explanation' => 'Incorrect: Inaction allows further damage.'],
                    ['text' => 'Change passwords and report', 'correct' => true, 'explanation' => 'Correct: Immediate action limits damage.'],
                    ['text' => 'Post online', 'correct' => false, 'explanation' => 'Incorrect: Doesn\'t secure the account.'],
                    ['text' => 'Wait', 'correct' => false, 'explanation' => 'Incorrect: Delay increases risk.'],
                ]
            ],
            [
                'question' => 'Why is reporting suspicious activity important?',
                'answers' => [
                    ['text' => 'To assign blame', 'correct' => false, 'explanation' => 'Incorrect: Not the purpose.'],
                    ['text' => 'To contain threats quickly', 'correct' => true, 'explanation' => 'Correct: Early reporting reduces impact.'],
                    ['text' => 'To delete evidence', 'correct' => false, 'explanation' => 'Incorrect: Evidence should be preserved.'],
                    ['text' => 'To warn friends', 'correct' => false, 'explanation' => 'Incorrect: Report through proper channels.'],
                ]
            ],
            [
                'question' => 'Shared devices are risky because they:',
                'answers' => [
                    ['text' => 'Are slower', 'correct' => false, 'explanation' => 'Incorrect: Speed isn\'t the security concern.'],
                    ['text' => 'Can expose logged-in sessions', 'correct' => true, 'explanation' => 'Correct: Open sessions allow unauthorized access.'],
                    ['text' => 'Block updates', 'correct' => false, 'explanation' => 'Incorrect: Not the main risk.'],
                    ['text' => 'Disable MFA', 'correct' => false, 'explanation' => 'Incorrect: Devices don\'t disable MFA.'],
                ]
            ],
            [
                'question' => 'Cybersecurity combines which elements?',
                'answers' => [
                    ['text' => 'Tools only', 'correct' => false, 'explanation' => 'Incorrect: Too narrow.'],
                    ['text' => 'Policies only', 'correct' => false, 'explanation' => 'Incorrect: Too narrow.'],
                    ['text' => 'People, processes, and technology', 'correct' => true, 'explanation' => 'Correct: Security requires a holistic approach.'],
                    ['text' => 'Firewalls only', 'correct' => false, 'explanation' => 'Incorrect: Too narrow.'],
                ]
            ],
            [
                'question' => 'Which habit best reduces human error?',
                'answers' => [
                    ['text' => 'Acting fast', 'correct' => false, 'explanation' => 'Incorrect: Speed increases mistakes.'],
                    ['text' => 'Trusting urgency', 'correct' => false, 'explanation' => 'Incorrect: Urgency is a manipulation tactic.'],
                    ['text' => 'Pausing before clicking', 'correct' => true, 'explanation' => 'Correct: Pausing allows verification.'],
                    ['text' => 'Ignoring warnings', 'correct' => false, 'explanation' => 'Incorrect: Warnings provide important alerts.'],
                ]
            ],
        ];
    }

    private function getLesson2Questions(): array
    {
        return [
            [
                'question' => 'What is social engineering?',
                'answers' => [
                    ['text' => 'A software vulnerability', 'correct' => false, 'explanation' => 'Incorrect: SE targets people, not software.'],
                    ['text' => 'A hardware failure', 'correct' => false, 'explanation' => 'Incorrect: SE is not hardware-related.'],
                    ['text' => 'Manipulation of people to gain information or access', 'correct' => true, 'explanation' => 'Correct: Social engineering exploits human behavior rather than technology.'],
                    ['text' => 'A network outage', 'correct' => false, 'explanation' => 'Incorrect: SE is about manipulation.'],
                ]
            ],
            [
                'question' => 'Social engineering attacks primarily target:',
                'answers' => [
                    ['text' => 'Firewalls', 'correct' => false, 'explanation' => 'Incorrect: SE targets people.'],
                    ['text' => 'Servers', 'correct' => false, 'explanation' => 'Incorrect: SE targets people.'],
                    ['text' => 'Human behavior and psychology', 'correct' => true, 'explanation' => 'Correct: These attacks rely on manipulating people.'],
                    ['text' => 'Power systems', 'correct' => false, 'explanation' => 'Incorrect: SE targets people.'],
                ]
            ],
            [
                'question' => 'Which psychological trigger involves pretending to be a person of power?',
                'answers' => [
                    ['text' => 'Scarcity', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'Authority', 'correct' => true, 'explanation' => 'Correct: Authority pressures victims to comply.'],
                    ['text' => 'Reciprocity', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'Familiarity', 'correct' => false, 'explanation' => 'Incorrect.'],
                ]
            ],
            [
                'question' => 'Creating panic or pressure with deadlines is an example of:',
                'answers' => [
                    ['text' => 'Authority', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'Fear', 'correct' => false, 'explanation' => 'Incorrect: Close, but urgency is more precise.'],
                    ['text' => 'Urgency', 'correct' => true, 'explanation' => 'Correct: Urgency pushes victims to act quickly.'],
                    ['text' => 'Curiosity', 'correct' => false, 'explanation' => 'Incorrect.'],
                ]
            ],
            [
                'question' => 'Which trigger involves offering rewards to gain compliance?',
                'answers' => [
                    ['text' => 'Fear', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'Scarcity', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'Reciprocity', 'correct' => true, 'explanation' => 'Correct: Reciprocity exploits the desire to return favors.'],
                    ['text' => 'Authority', 'correct' => false, 'explanation' => 'Incorrect.'],
                ]
            ],
            [
                'question' => 'Why is urgency effective in social engineering?',
                'answers' => [
                    ['text' => 'It improves system speed', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'It prevents logical thinking', 'correct' => true, 'explanation' => 'Correct: Urgency causes people to act without verifying.'],
                    ['text' => 'It strengthens passwords', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'It encrypts data', 'correct' => false, 'explanation' => 'Incorrect.'],
                ]
            ],
            [
                'question' => 'What is the safest first action when receiving a suspicious message?',
                'answers' => [
                    ['text' => 'Reply to ask questions', 'correct' => false, 'explanation' => 'Incorrect: Engagement is risky.'],
                    ['text' => 'Click links to check', 'correct' => false, 'explanation' => 'Incorrect: May trigger malware.'],
                    ['text' => 'Pause and avoid interacting', 'correct' => true, 'explanation' => 'Correct: Pausing prevents mistakes.'],
                    ['text' => 'Forward it to others', 'correct' => false, 'explanation' => 'Incorrect: May spread the threat.'],
                ]
            ],
            [
                'question' => 'Verification should be done using:',
                'answers' => [
                    ['text' => 'Contact details in the message', 'correct' => false, 'explanation' => 'Incorrect: May be attacker-controlled.'],
                    ['text' => 'A trusted, known channel', 'correct' => true, 'explanation' => 'Correct: Attackers control message-provided contacts.'],
                    ['text' => 'Social media comments', 'correct' => false, 'explanation' => 'Incorrect: Not secure.'],
                    ['text' => 'Group chats', 'correct' => false, 'explanation' => 'Incorrect: Not verified channel.'],
                ]
            ],
            [
                'question' => 'Which information should NEVER be shared via email or text?',
                'answers' => [
                    ['text' => 'Full name', 'correct' => false, 'explanation' => 'Incorrect: Generally safe to share.'],
                    ['text' => 'OTP or password', 'correct' => true, 'explanation' => 'Correct: OTPs and passwords must remain secret.'],
                    ['text' => 'Job title', 'correct' => false, 'explanation' => 'Incorrect: Generally safe to share.'],
                    ['text' => 'Office hours', 'correct' => false, 'explanation' => 'Incorrect: Generally safe to share.'],
                ]
            ],
            [
                'question' => 'A message claiming to be from IT asking for your password is most likely:',
                'answers' => [
                    ['text' => 'Legitimate', 'correct' => false, 'explanation' => 'Incorrect: IT never asks for passwords.'],
                    ['text' => 'A routine check', 'correct' => false, 'explanation' => 'Incorrect: Not a legitimate practice.'],
                    ['text' => 'A social engineering attempt', 'correct' => true, 'explanation' => 'Correct: IT will never ask for passwords.'],
                    ['text' => 'A policy update', 'correct' => false, 'explanation' => 'Incorrect: Policies don\'t require password sharing.'],
                ]
            ],
            [
                'question' => 'Which step comes AFTER pausing in the safe response model?',
                'answers' => [
                    ['text' => 'Ignore', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'Verify through a trusted channel', 'correct' => true, 'explanation' => 'Correct: Verification confirms legitimacy.'],
                    ['text' => 'Delete immediately', 'correct' => false, 'explanation' => 'Incorrect: Should preserve evidence.'],
                    ['text' => 'Share publicly', 'correct' => false, 'explanation' => 'Incorrect.'],
                ]
            ],
            [
                'question' => 'Why should you not delete suspicious messages immediately?',
                'answers' => [
                    ['text' => 'They may be useful evidence', 'correct' => true, 'explanation' => 'Correct: Evidence helps investigation.'],
                    ['text' => 'They slow devices', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'They trigger alerts', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'They block accounts', 'correct' => false, 'explanation' => 'Incorrect.'],
                ]
            ],
            [
                'question' => 'Social engineering attacks can occur through:',
                'answers' => [
                    ['text' => 'Email only', 'correct' => false, 'explanation' => 'Incorrect: Multiple channels exist.'],
                    ['text' => 'Phone calls only', 'correct' => false, 'explanation' => 'Incorrect: Multiple channels exist.'],
                    ['text' => 'Digital and voice channels', 'correct' => true, 'explanation' => 'Correct: Attacks use multiple communication channels.'],
                    ['text' => 'Servers only', 'correct' => false, 'explanation' => 'Incorrect: SE targets people.'],
                ]
            ],
            [
                'question' => 'Which scenario best describes social engineering?',
                'answers' => [
                    ['text' => 'A virus corrupting files', 'correct' => false, 'explanation' => 'Incorrect: Technical attack.'],
                    ['text' => 'A hacker exploiting code', 'correct' => false, 'explanation' => 'Incorrect: Technical attack.'],
                    ['text' => 'A fake bank call asking for an OTP', 'correct' => true, 'explanation' => 'Correct: The attacker manipulates the victim directly.'],
                    ['text' => 'A server crash', 'correct' => false, 'explanation' => 'Incorrect: Technical failure.'],
                ]
            ],
            [
                'question' => 'Which action increases your risk during an attack?',
                'answers' => [
                    ['text' => 'Pausing', 'correct' => false, 'explanation' => 'Incorrect: Pausing is protective.'],
                    ['text' => 'Verifying', 'correct' => false, 'explanation' => 'Incorrect: Verification is protective.'],
                    ['text' => 'Acting quickly without checking', 'correct' => true, 'explanation' => 'Correct: Speed without verification leads to mistakes.'],
                    ['text' => 'Reporting', 'correct' => false, 'explanation' => 'Incorrect: Reporting is protective.'],
                ]
            ],
            [
                'question' => 'Legitimate organizations usually communicate requests for action via:',
                'answers' => [
                    ['text' => 'Unofficial chat apps', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'Random phone calls', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'Official portals or known channels', 'correct' => true, 'explanation' => 'Correct: Official channels reduce spoofing risk.'],
                    ['text' => 'Anonymous emails', 'correct' => false, 'explanation' => 'Incorrect.'],
                ]
            ],
            [
                'question' => 'Which goal do attackers usually have?',
                'answers' => [
                    ['text' => 'Educate users', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'Gain money, access, or data', 'correct' => true, 'explanation' => 'Correct: Social engineering aims to exploit victims.'],
                    ['text' => 'Improve security', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'Test systems', 'correct' => false, 'explanation' => 'Incorrect: Not typical attacker goal.'],
                ]
            ],
            [
                'question' => 'Why is reporting social engineering important?',
                'answers' => [
                    ['text' => 'To embarrass senders', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'To help others avoid the same scam', 'correct' => true, 'explanation' => 'Correct: Reporting helps stop spread.'],
                    ['text' => 'To delete messages', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'To respond emotionally', 'correct' => false, 'explanation' => 'Incorrect.'],
                ]
            ],
            [
                'question' => 'Which behavior best protects against social engineering?',
                'answers' => [
                    ['text' => 'Trusting authority', 'correct' => false, 'explanation' => 'Incorrect: Verify first.'],
                    ['text' => 'Acting immediately', 'correct' => false, 'explanation' => 'Incorrect: Pause first.'],
                    ['text' => 'Verifying before acting', 'correct' => true, 'explanation' => 'Correct: Verification blocks manipulation.'],
                    ['text' => 'Ignoring policies', 'correct' => false, 'explanation' => 'Incorrect: Policies help protect.'],
                ]
            ],
            [
                'question' => 'Social engineering is effective mainly because it exploits:',
                'answers' => [
                    ['text' => 'Software bugs', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'Hardware limits', 'correct' => false, 'explanation' => 'Incorrect.'],
                    ['text' => 'Human psychology and trust', 'correct' => true, 'explanation' => 'Correct: Human behavior is the weakest link.'],
                    ['text' => 'Network speed', 'correct' => false, 'explanation' => 'Incorrect.'],
                ]
            ],
        ];
    }

    // Continue with remaining question methods (Lessons 3-13)...
    // Due to space, I'll provide a template structure for the remaining lessons

    private function getLesson3Questions(): array
    {
        return [
            ['question' => 'What is phishing?', 'answers' => [
                ['text' => 'A phone-based scam', 'correct' => false, 'explanation' => 'Incorrect: That\'s vishing.'],
                ['text' => 'A text message scam', 'correct' => false, 'explanation' => 'Incorrect: That\'s smishing.'],
                ['text' => 'An email-based scam designed to steal information', 'correct' => true, 'explanation' => 'Correct: Phishing attacks are primarily delivered via email.'],
                ['text' => 'A system update', 'correct' => false, 'explanation' => 'Incorrect: Not related to phishing.'],
            ]],
            ['question' => 'Smishing attacks are delivered through:', 'answers' => [
                ['text' => 'Email', 'correct' => false, 'explanation' => 'Incorrect: That\'s phishing.'],
                ['text' => 'Phone calls', 'correct' => false, 'explanation' => 'Incorrect: That\'s vishing.'],
                ['text' => 'SMS or messaging apps', 'correct' => true, 'explanation' => 'Correct: Smishing uses text messages or chat apps.'],
                ['text' => 'Servers', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'Vishing attacks occur through:', 'answers' => [
                ['text' => 'Emails', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'SMS', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Voice calls', 'correct' => true, 'explanation' => 'Correct: Vishing uses voice communication.'],
                ['text' => 'Websites', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'Which request is a major red flag?', 'answers' => [
                ['text' => 'Password or OTP request', 'correct' => true, 'explanation' => 'Correct: Legitimate organizations do not ask for OTPs or passwords.'],
                ['text' => 'Account notification', 'correct' => false, 'explanation' => 'Incorrect: May be legitimate.'],
                ['text' => 'Policy reminder', 'correct' => false, 'explanation' => 'Incorrect: May be legitimate.'],
                ['text' => 'Scheduled maintenance notice', 'correct' => false, 'explanation' => 'Incorrect: May be legitimate.'],
            ]],
            ['question' => 'Why are shortened links risky?', 'answers' => [
                ['text' => 'They improve speed', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'They hide the real destination', 'correct' => true, 'explanation' => 'Correct: Shortened links conceal malicious sites.'],
                ['text' => 'They block malware', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'They encrypt data', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'What should you do first when receiving a suspicious email or text?', 'answers' => [
                ['text' => 'Click to check', 'correct' => false, 'explanation' => 'Incorrect: May trigger malware.'],
                ['text' => 'Reply immediately', 'correct' => false, 'explanation' => 'Incorrect: Confirms your contact.'],
                ['text' => 'Pause and avoid interacting', 'correct' => true, 'explanation' => 'Correct: Pausing prevents accidental compromise.'],
                ['text' => 'Forward it', 'correct' => false, 'explanation' => 'Incorrect: May spread the threat.'],
            ]],
            ['question' => 'Caller ID alone should be trusted.', 'answers' => [
                ['text' => 'True', 'correct' => false, 'explanation' => 'Incorrect: Caller ID can be spoofed.'],
                ['text' => 'False', 'correct' => true, 'explanation' => 'Correct: Caller ID can be spoofed by attackers.'],
            ]],
            ['question' => 'Which attachment type is especially risky in phishing emails?', 'answers' => [
                ['text' => 'PDF', 'correct' => false, 'explanation' => 'Incorrect: Can be safe if from trusted sources.'],
                ['text' => 'Image file', 'correct' => false, 'explanation' => 'Incorrect: Generally safer.'],
                ['text' => 'ZIP or HTML files', 'correct' => true, 'explanation' => 'Correct: These file types can contain malicious code.'],
                ['text' => 'Text file', 'correct' => false, 'explanation' => 'Incorrect: Generally safer.'],
            ]],
            ['question' => 'The safest way to verify a bank-related message is to:', 'answers' => [
                ['text' => 'Reply to the message', 'correct' => false, 'explanation' => 'Incorrect: May be attacker-controlled.'],
                ['text' => 'Call the number in the message', 'correct' => false, 'explanation' => 'Incorrect: Number may be fake.'],
                ['text' => 'Use the official app or hotline yourself', 'correct' => true, 'explanation' => 'Correct: Independent verification avoids attacker-controlled channels.'],
                ['text' => 'Ask friends', 'correct' => false, 'explanation' => 'Incorrect: Not reliable.'],
            ]],
            ['question' => 'What should you never share over phone or message?', 'answers' => [
                ['text' => 'Name', 'correct' => false, 'explanation' => 'Incorrect: Generally safe.'],
                ['text' => 'OTP or password', 'correct' => true, 'explanation' => 'Correct: OTPs and passwords must remain secret.'],
                ['text' => 'Office location', 'correct' => false, 'explanation' => 'Incorrect: Generally safe.'],
                ['text' => 'Job title', 'correct' => false, 'explanation' => 'Incorrect: Generally safe.'],
            ]],
            ['question' => 'Smishing messages often try to make victims:', 'answers' => [
                ['text' => 'Relax', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Act urgently', 'correct' => true, 'explanation' => 'Correct: Urgency pressures quick action.'],
                ['text' => 'Ignore alerts', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Log out', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'Why should evidence be preserved before deleting a scam message?', 'answers' => [
                ['text' => 'It uses storage', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'It helps security teams investigate', 'correct' => true, 'explanation' => 'Correct: Evidence supports response and prevention.'],
                ['text' => 'It spreads malware', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'It blocks accounts', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'What is a safe action after receiving a vishing call?', 'answers' => [
                ['text' => 'Continue the call', 'correct' => false, 'explanation' => 'Incorrect: May lead to manipulation.'],
                ['text' => 'Share information', 'correct' => false, 'explanation' => 'Incorrect: Never share sensitive info.'],
                ['text' => 'Hang up and call the official number yourself', 'correct' => true, 'explanation' => 'Correct: Independent contact blocks spoofing.'],
                ['text' => 'Argue with the caller', 'correct' => false, 'explanation' => 'Incorrect: Wastes time.'],
            ]],
            ['question' => 'Which channel is commonly used for small fake payment requests?', 'answers' => [
                ['text' => 'Email', 'correct' => false, 'explanation' => 'Incorrect: Less common for small amounts.'],
                ['text' => 'SMS or messaging apps', 'correct' => true, 'explanation' => 'Correct: Smishing often requests small payments.'],
                ['text' => 'Printed letters', 'correct' => false, 'explanation' => 'Incorrect: Not common for scams.'],
                ['text' => 'System logs', 'correct' => false, 'explanation' => 'Incorrect: Not a communication channel.'],
            ]],
            ['question' => 'MFA helps reduce damage because it:', 'answers' => [
                ['text' => 'Removes passwords', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Blocks all attacks', 'correct' => false, 'explanation' => 'Incorrect: No tool blocks everything.'],
                ['text' => 'Adds an extra verification step', 'correct' => true, 'explanation' => 'Correct: MFA limits credential misuse.'],
                ['text' => 'Encrypts emails', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'What is a common vishing tactic?', 'answers' => [
                ['text' => 'Fake login pages', 'correct' => false, 'explanation' => 'Incorrect: That\'s phishing.'],
                ['text' => 'Malicious attachments', 'correct' => false, 'explanation' => 'Incorrect: That\'s phishing.'],
                ['text' => 'Impersonating banks or IT support', 'correct' => true, 'explanation' => 'Correct: Vishing relies on voice impersonation.'],
                ['text' => 'Software updates', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'Which behavior increases risk?', 'answers' => [
                ['text' => 'Verifying requests', 'correct' => false, 'explanation' => 'Incorrect: Verification protects.'],
                ['text' => 'Acting immediately under pressure', 'correct' => true, 'explanation' => 'Correct: Speed without verification leads to compromise.'],
                ['text' => 'Reporting incidents', 'correct' => false, 'explanation' => 'Incorrect: Reporting helps.'],
                ['text' => 'Pausing', 'correct' => false, 'explanation' => 'Incorrect: Pausing protects.'],
            ]],
            ['question' => 'Updates help protect against phishing by:', 'answers' => [
                ['text' => 'Removing links', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Blocking known exploits', 'correct' => true, 'explanation' => 'Correct: Updates patch vulnerabilities used by attackers.'],
                ['text' => 'Slowing systems', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Disabling browsers', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'Which action should follow verification?', 'answers' => [
                ['text' => 'Ignore', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Report the incident', 'correct' => true, 'explanation' => 'Correct: Reporting helps contain threats.'],
                ['text' => 'Delete evidence first', 'correct' => false, 'explanation' => 'Incorrect: Evidence should be preserved.'],
                ['text' => 'Share publicly', 'correct' => false, 'explanation' => 'Incorrect: May cause panic.'],
            ]],
            ['question' => 'Phishing, smishing, and vishing are dangerous because they exploit:', 'answers' => [
                ['text' => 'Network cables', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Hardware limits', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Human trust and behavior', 'correct' => true, 'explanation' => 'Correct: These attacks manipulate people, not systems.'],
                ['text' => 'Internet speed', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
        ];
    }

    // Add similar question arrays for Lessons 4-13...
    // I'll provide one more complete example (Lesson 4) and then templates for the rest
    
    private function getLesson4Questions(): array
    {
        return [
            ['question' => 'What is social media impersonation?', 'answers' => [
                ['text' => 'Losing internet access', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'A fake profile pretending to be a real person', 'correct' => true, 'explanation' => 'Correct: Impersonation involves fake accounts using another person\'s identity.'],
                ['text' => 'A system update', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'A private message', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'A hijacked account is best described as:', 'answers' => [
                ['text' => 'A newly created fake profile', 'correct' => false, 'explanation' => 'Incorrect: That\'s impersonation.'],
                ['text' => 'A real account taken over by an attacker', 'correct' => true, 'explanation' => 'Correct: Hijacking involves unauthorized access to a real account.'],
                ['text' => 'A deleted account', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'A verified account', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'Which is a common sign of impersonation?', 'answers' => [
                ['text' => 'Years of post history', 'correct' => false, 'explanation' => 'Incorrect: Suggests authenticity.'],
                ['text' => 'Verified badge', 'correct' => false, 'explanation' => 'Incorrect: Suggests authenticity.'],
                ['text' => 'Duplicate profile with copied photos', 'correct' => true, 'explanation' => 'Correct: Impersonation accounts often reuse stolen photos.'],
                ['text' => 'Official email', 'correct' => false, 'explanation' => 'Incorrect: Suggests authenticity.'],
            ]],
            ['question' => 'Sudden messages asking for money or codes are a sign of:', 'answers' => [
                ['text' => 'Normal behavior', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Platform updates', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Possible impersonation or hijacking', 'correct' => true, 'explanation' => 'Correct: These requests are common scam tactics.'],
                ['text' => 'Account verification', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'Why are hijacked accounts dangerous?', 'answers' => [
                ['text' => 'They reduce storage', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'They appear trustworthy to contacts', 'correct' => true, 'explanation' => 'Correct: Familiar accounts increase victim trust.'],
                ['text' => 'They block messages', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'They disable MFA', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'What is the safest way to verify a suspicious request from a friend?', 'answers' => [
                ['text' => 'Reply in the same chat', 'correct' => false, 'explanation' => 'Incorrect: Stays within attacker control.'],
                ['text' => 'Ask for more details', 'correct' => false, 'explanation' => 'Incorrect: Attacker can provide fabricated details.'],
                ['text' => 'Contact them through a known channel', 'correct' => true, 'explanation' => 'Correct: Out-of-band verification confirms identity.'],
                ['text' => 'Share the message publicly', 'correct' => false, 'explanation' => 'Incorrect: May spread panic.'],
            ]],
            ['question' => 'Which request is a major red flag?', 'answers' => [
                ['text' => 'Asking about your day', 'correct' => false, 'explanation' => 'Incorrect: Normal conversation.'],
                ['text' => 'Asking for gift cards or e-wallet codes', 'correct' => true, 'explanation' => 'Correct: Gift card requests are common scams.'],
                ['text' => 'Sharing photos', 'correct' => false, 'explanation' => 'Incorrect: Normal behavior.'],
                ['text' => 'Posting updates', 'correct' => false, 'explanation' => 'Incorrect: Normal behavior.'],
            ]],
            ['question' => 'If your account is hijacked, what should you do first?', 'answers' => [
                ['text' => 'Ignore it', 'correct' => false, 'explanation' => 'Incorrect: Allows continued misuse.'],
                ['text' => 'Change your password immediately', 'correct' => true, 'explanation' => 'Correct: Changing the password cuts off attacker access.'],
                ['text' => 'Create a new account', 'correct' => false, 'explanation' => 'Incorrect: Doesn\'t secure the compromised account.'],
                ['text' => 'Post publicly', 'correct' => false, 'explanation' => 'Incorrect: Should secure first, then notify.'],
            ]],
            ['question' => 'Ending unknown sessions helps because it:', 'answers' => [
                ['text' => 'Deletes posts', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Removes attacker access', 'correct' => true, 'explanation' => 'Correct: Unknown sessions indicate unauthorized access.'],
                ['text' => 'Improves speed', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Increases storage', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'Why should you warn your contacts after hijacking?', 'answers' => [
                ['text' => 'To embarrass attackers', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'To prevent others from being scammed', 'correct' => true, 'explanation' => 'Correct: Warning contacts stops scam spread.'],
                ['text' => 'To gain followers', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'To delete messages', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'Which security feature helps prevent account takeover?', 'answers' => [
                ['text' => 'Public profiles', 'correct' => false, 'explanation' => 'Incorrect: Increases exposure.'],
                ['text' => 'Password reuse', 'correct' => false, 'explanation' => 'Incorrect: Weakens security.'],
                ['text' => 'Multi-factor authentication (MFA)', 'correct' => true, 'explanation' => 'Correct: MFA adds an extra protection layer.'],
                ['text' => 'Weak passwords', 'correct' => false, 'explanation' => 'Incorrect: Weakens security.'],
            ]],
            ['question' => 'Impersonation profiles are often:', 'answers' => [
                ['text' => 'Years old', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Verified', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Newly created with little activity', 'correct' => true, 'explanation' => 'Correct: Fake profiles lack history.'],
                ['text' => 'Private company pages', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'Requests to move conversations off-platform are risky because:', 'answers' => [
                ['text' => 'They improve privacy', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'They avoid platform protections', 'correct' => true, 'explanation' => 'Correct: Off-platform chats bypass safety tools.'],
                ['text' => 'They reduce storage', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'They block messages', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'What information should be limited on public profiles?', 'answers' => [
                ['text' => 'Hobbies', 'correct' => false, 'explanation' => 'Incorrect: Generally safe.'],
                ['text' => 'Personal identifiers and contact details', 'correct' => true, 'explanation' => 'Correct: Limiting personal data reduces targeting.'],
                ['text' => 'Favorite music', 'correct' => false, 'explanation' => 'Incorrect: Generally safe.'],
                ['text' => 'Public photos', 'correct' => false, 'explanation' => 'Incorrect: Generally safe.'],
            ]],
            ['question' => 'Reporting fake profiles is important because it:', 'answers' => [
                ['text' => 'Deletes your account', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Helps platforms remove malicious users', 'correct' => true, 'explanation' => 'Correct: Reporting protects others.'],
                ['text' => 'Slows the system', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Blocks friends', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'Which behavior increases risk of impersonation scams?', 'answers' => [
                ['text' => 'Verifying requests', 'correct' => false, 'explanation' => 'Incorrect: Protective.'],
                ['text' => 'Accepting unknown friend requests', 'correct' => true, 'explanation' => 'Correct: Unknown contacts increase exposure.'],
                ['text' => 'Using MFA', 'correct' => false, 'explanation' => 'Incorrect: Protective.'],
                ['text' => 'Strong passwords', 'correct' => false, 'explanation' => 'Incorrect: Protective.'],
            ]],
            ['question' => 'A sudden change in posting behavior may indicate:', 'answers' => [
                ['text' => 'Account hijacking', 'correct' => true, 'explanation' => 'Correct: Behavior changes are warning signs.'],
                ['text' => 'Platform upgrade', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Normal activity', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Profile verification', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'Which action should follow verification of a scam?', 'answers' => [
                ['text' => 'Ignore', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'Report the account or message', 'correct' => true, 'explanation' => 'Correct: Reporting helps stop scams.'],
                ['text' => 'Argue with the attacker', 'correct' => false, 'explanation' => 'Incorrect: Wastes time.'],
                ['text' => 'Share publicly', 'correct' => false, 'explanation' => 'Incorrect: May cause issues.'],
            ]],
            ['question' => 'Why is impersonation effective?', 'answers' => [
                ['text' => 'It uses advanced malware', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'It exploits trust and familiarity', 'correct' => true, 'explanation' => 'Correct: Familiar identities lower suspicion.'],
                ['text' => 'It speeds communication', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'It encrypts messages', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'The best overall defense against impersonation is to:', 'answers' => [
                ['text' => 'Trust all contacts', 'correct' => false, 'explanation' => 'Incorrect: Always verify.'],
                ['text' => 'Verify identities through trusted channels', 'correct' => true, 'explanation' => 'Correct: Verification prevents deception.'],
                ['text' => 'Disable social media', 'correct' => false, 'explanation' => 'Incorrect: Not practical.'],
                ['text' => 'Share less information privately', 'correct' => false, 'explanation' => 'Incorrect: Verification is key.'],
            ]],
        ];
    }

        private function getLesson5Questions(): array
    {
        return [
            ['question' => 'What is identity theft?', 'answers' => [
                ['text' => 'Losing a device', 'correct' => false, 'explanation' => 'Incorrect: That\'s device loss, not identity theft.'],
                ['text' => 'Unauthorized use of someone\'s personal information', 'correct' => true, 'explanation' => 'Correct: Identity theft involves misuse of personal data.'],
                ['text' => 'Forgetting a password', 'correct' => false, 'explanation' => 'Incorrect: That\'s a password issue.'],
                ['text' => 'A system crash', 'correct' => false, 'explanation' => 'Incorrect: That\'s a technical issue.'],
            ]],
            ['question' => 'Which is considered sensitive data?', 'answers' => [
                ['text' => 'Favorite color', 'correct' => false, 'explanation' => 'Incorrect: Not sensitive.'],
                ['text' => 'OTP or password', 'correct' => true, 'explanation' => 'Correct: OTPs and passwords can lead to account compromise.'],
                ['text' => 'Job title', 'correct' => false, 'explanation' => 'Incorrect: Generally not sensitive.'],
                ['text' => 'Public post', 'correct' => false, 'explanation' => 'Incorrect: Already public.'],
            ]],
            ['question' => 'Identity theft often begins with:', 'answers' => [
                ['text' => 'Hardware failure', 'correct' => false, 'explanation' => 'Incorrect: Technical issue.'],
                ['text' => 'Social engineering or phishing attacks', 'correct' => true, 'explanation' => 'Correct: Many identity theft cases start with scams.'],
                ['text' => 'Software updates', 'correct' => false, 'explanation' => 'Incorrect: Updates improve security.'],
                ['text' => 'System maintenance', 'correct' => false, 'explanation' => 'Incorrect: Routine operation.'],
            ]],
            ['question' => 'Why is reused personal data dangerous?', 'answers' => [
                ['text' => 'It saves time', 'correct' => false, 'explanation' => 'Incorrect: Convenience doesn\'t reduce risk.'],
                ['text' => 'It improves security', 'correct' => false, 'explanation' => 'Incorrect: Reuse increases risk.'],
                ['text' => 'It makes future scams more convincing', 'correct' => true, 'explanation' => 'Correct: Attackers reuse data to gain trust.'],
                ['text' => 'It reduces storage', 'correct' => false, 'explanation' => 'Incorrect: Unrelated to security.'],
            ]],
            ['question' => 'What does "minimal data sharing" mean?', 'answers' => [
                ['text' => 'Sharing everything', 'correct' => false, 'explanation' => 'Incorrect: Opposite of minimal.'],
                ['text' => 'Sharing only what is necessary', 'correct' => true, 'explanation' => 'Correct: Limiting data reduces exposure.'],
                ['text' => 'Sharing on social media', 'correct' => false, 'explanation' => 'Incorrect: Public sharing increases risk.'],
                ['text' => 'Sharing with friends', 'correct' => false, 'explanation' => 'Incorrect: Still may be excessive.'],
            ]],
            ['question' => 'Which channel is safest for submitting official documents?', 'answers' => [
                ['text' => 'Personal email', 'correct' => false, 'explanation' => 'Incorrect: Lacks security controls.'],
                ['text' => 'Messaging apps', 'correct' => false, 'explanation' => 'Incorrect: Lacks security controls.'],
                ['text' => 'Approved official portals', 'correct' => true, 'explanation' => 'Correct: Official portals have security controls.'],
                ['text' => 'Public cloud folders', 'correct' => false, 'explanation' => 'Incorrect: May be exposed.'],
            ]],
            ['question' => 'Sending photos of IDs through chat apps is:', 'answers' => [
                ['text' => 'Safe', 'correct' => false, 'explanation' => 'Incorrect: Chat apps lack proper security.'],
                ['text' => 'Recommended', 'correct' => false, 'explanation' => 'Incorrect: Not a secure practice.'],
                ['text' => 'Risky and should be avoided', 'correct' => true, 'explanation' => 'Correct: Chat apps lack proper security controls.'],
                ['text' => 'Required', 'correct' => false, 'explanation' => 'Incorrect: Never required via chat.'],
            ]],
            ['question' => 'Redacting sensitive data means:', 'answers' => [
                ['text' => 'Deleting files', 'correct' => false, 'explanation' => 'Incorrect: Redaction is selective hiding.'],
                ['text' => 'Hiding unnecessary sensitive information', 'correct' => true, 'explanation' => 'Correct: Redaction limits exposure.'],
                ['text' => 'Sharing full details', 'correct' => false, 'explanation' => 'Incorrect: Opposite of redaction.'],
                ['text' => 'Posting publicly', 'correct' => false, 'explanation' => 'Incorrect: Opposite of redaction.'],
            ]],
            ['question' => 'Which practice best protects against identity theft?', 'answers' => [
                ['text' => 'Weak passwords', 'correct' => false, 'explanation' => 'Incorrect: Increases risk.'],
                ['text' => 'Password reuse', 'correct' => false, 'explanation' => 'Incorrect: Increases risk.'],
                ['text' => 'Strong passwords with MFA', 'correct' => true, 'explanation' => 'Correct: Strong authentication reduces risk.'],
                ['text' => 'Ignoring alerts', 'correct' => false, 'explanation' => 'Incorrect: Increases risk.'],
            ]],
            ['question' => 'What should you do first after suspected data exposure?', 'answers' => [
                ['text' => 'Wait and observe', 'correct' => false, 'explanation' => 'Incorrect: Delay increases risk.'],
                ['text' => 'Change passwords and secure accounts', 'correct' => true, 'explanation' => 'Correct: Immediate action limits damage.'],
                ['text' => 'Post on social media', 'correct' => false, 'explanation' => 'Incorrect: Doesn\'t secure accounts.'],
                ['text' => 'Ignore it', 'correct' => false, 'explanation' => 'Incorrect: Allows continued exposure.'],
            ]],
            ['question' => 'Which action helps detect misuse early?', 'answers' => [
                ['text' => 'Ignoring account activity', 'correct' => false, 'explanation' => 'Incorrect: Monitoring is needed.'],
                ['text' => 'Monitoring accounts for unusual activity', 'correct' => true, 'explanation' => 'Correct: Monitoring allows early response.'],
                ['text' => 'Deleting messages', 'correct' => false, 'explanation' => 'Incorrect: Doesn\'t help detection.'],
                ['text' => 'Sharing data', 'correct' => false, 'explanation' => 'Incorrect: Increases risk.'],
            ]],
            ['question' => 'Lost or stolen devices increase risk because they:', 'answers' => [
                ['text' => 'Reduce battery life', 'correct' => false, 'explanation' => 'Incorrect: Not a security issue.'],
                ['text' => 'May contain stored personal data', 'correct' => true, 'explanation' => 'Correct: Stored data can be accessed by attackers.'],
                ['text' => 'Slow networks', 'correct' => false, 'explanation' => 'Incorrect: Not related.'],
                ['text' => 'Disable MFA', 'correct' => false, 'explanation' => 'Incorrect: MFA remains active.'],
            ]],
            ['question' => 'Why should sensitive documents not be stored on unsecured devices?', 'answers' => [
                ['text' => 'They use space', 'correct' => false, 'explanation' => 'Incorrect: Storage isn\'t the security concern.'],
                ['text' => 'They are slow to open', 'correct' => false, 'explanation' => 'Incorrect: Speed isn\'t the concern.'],
                ['text' => 'They can be accessed if the device is compromised', 'correct' => true, 'explanation' => 'Correct: Unsecured storage increases exposure.'],
                ['text' => 'They require updates', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Reporting suspected identity theft helps because it:', 'answers' => [
                ['text' => 'Assigns blame', 'correct' => false, 'explanation' => 'Incorrect: Not the purpose.'],
                ['text' => 'Limits further misuse of data', 'correct' => true, 'explanation' => 'Correct: Reporting enables protective actions.'],
                ['text' => 'Deletes accounts', 'correct' => false, 'explanation' => 'Incorrect: Reporting doesn\'t delete accounts.'],
                ['text' => 'Slows systems', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Which behavior increases identity theft risk?', 'answers' => [
                ['text' => 'Using MFA', 'correct' => false, 'explanation' => 'Incorrect: MFA protects.'],
                ['text' => 'Oversharing personal details online', 'correct' => true, 'explanation' => 'Correct: Oversharing gives attackers data.'],
                ['text' => 'Strong passwords', 'correct' => false, 'explanation' => 'Incorrect: Strong passwords protect.'],
                ['text' => 'Using official portals', 'correct' => false, 'explanation' => 'Incorrect: Official portals protect.'],
            ]],
            ['question' => 'Attackers often combine stolen data from multiple sources to:', 'answers' => [
                ['text' => 'Improve backups', 'correct' => false, 'explanation' => 'Incorrect: Attackers don\'t backup data.'],
                ['text' => 'Create convincing impersonation attempts', 'correct' => true, 'explanation' => 'Correct: Combined data increases believability.'],
                ['text' => 'Reduce scams', 'correct' => false, 'explanation' => 'Incorrect: Opposite intent.'],
                ['text' => 'Fix accounts', 'correct' => false, 'explanation' => 'Incorrect: Attackers don\'t fix accounts.'],
            ]],
            ['question' => 'Why is MFA important after a data breach?', 'answers' => [
                ['text' => 'It removes passwords', 'correct' => false, 'explanation' => 'Incorrect: MFA doesn\'t remove passwords.'],
                ['text' => 'It blocks all attacks', 'correct' => false, 'explanation' => 'Incorrect: No tool blocks everything.'],
                ['text' => 'It adds an extra layer of protection', 'correct' => true, 'explanation' => 'Correct: MFA reduces account takeover risk.'],
                ['text' => 'It deletes data', 'correct' => false, 'explanation' => 'Incorrect: MFA doesn\'t delete data.'],
            ]],
            ['question' => 'Which information should be limited on public profiles?', 'answers' => [
                ['text' => 'Hobbies', 'correct' => false, 'explanation' => 'Incorrect: Generally safe.'],
                ['text' => 'Personal identifiers and contact details', 'correct' => true, 'explanation' => 'Correct: Limiting identifiers reduces targeting.'],
                ['text' => 'Public photos', 'correct' => false, 'explanation' => 'Incorrect: Generally safe.'],
                ['text' => 'Interests', 'correct' => false, 'explanation' => 'Incorrect: Generally safe.'],
            ]],
            ['question' => 'What is a long-term prevention strategy?', 'answers' => [
                ['text' => 'Ignoring security news', 'correct' => false, 'explanation' => 'Incorrect: Awareness is important.'],
                ['text' => 'Practicing cautious data sharing', 'correct' => true, 'explanation' => 'Correct: Caution reduces future risk.'],
                ['text' => 'Disabling accounts', 'correct' => false, 'explanation' => 'Incorrect: Not practical.'],
                ['text' => 'Sharing less privately', 'correct' => false, 'explanation' => 'Incorrect: Unclear and not best practice.'],
            ]],
            ['question' => 'Identity theft is dangerous mainly because it can:', 'answers' => [
                ['text' => 'Slow computers', 'correct' => false, 'explanation' => 'Incorrect: Not the main danger.'],
                ['text' => 'Lead to financial and personal harm', 'correct' => true, 'explanation' => 'Correct: Identity theft can cause serious long-term damage.'],
                ['text' => 'Improve security', 'correct' => false, 'explanation' => 'Incorrect: Opposite effect.'],
                ['text' => 'Delete files', 'correct' => false, 'explanation' => 'Incorrect: Not the primary danger.'],
            ]],
        ];
    }

    private function getLesson6Questions(): array
    {
        return [
            ['question' => 'What is an AI-enhanced scam?', 'answers' => [
                ['text' => 'A scam that only targets computers', 'correct' => false, 'explanation' => 'Incorrect: AI scams target people.'],
                ['text' => 'A scam that uses AI to appear more convincing', 'correct' => true, 'explanation' => 'Correct: AI can generate polished text, voices, or videos that increase believability.'],
                ['text' => 'A scam that requires malware to work', 'correct' => false, 'explanation' => 'Incorrect: AI scams can work without malware.'],
                ['text' => 'A scam that happens only on social media', 'correct' => false, 'explanation' => 'Incorrect: AI scams use multiple channels.'],
            ]],
            ['question' => 'A deepfake is best defined as:', 'answers' => [
                ['text' => 'A blurry video call', 'correct' => false, 'explanation' => 'Incorrect: Quality issues aren\'t deepfakes.'],
                ['text' => 'AI-generated or AI-altered media that imitates a real person', 'correct' => true, 'explanation' => 'Correct: Deepfakes can mimic faces, voices, and behaviors using AI.'],
                ['text' => 'A screenshot of a profile', 'correct' => false, 'explanation' => 'Incorrect: Not AI-generated.'],
                ['text' => 'A deleted message', 'correct' => false, 'explanation' => 'Incorrect: Unrelated to deepfakes.'],
            ]],
            ['question' => 'Why are AI-generated scam messages often harder to spot than older scams?', 'answers' => [
                ['text' => 'They always contain viruses', 'correct' => false, 'explanation' => 'Incorrect: Not always.'],
                ['text' => 'They often have professional grammar and formatting', 'correct' => true, 'explanation' => 'Correct: AI can remove common low-quality scam signs like bad grammar.'],
                ['text' => 'They are sent only at night', 'correct' => false, 'explanation' => 'Incorrect: Timing doesn\'t make them harder to spot.'],
                ['text' => 'They never include links', 'correct' => false, 'explanation' => 'Incorrect: They can include links.'],
            ]],
            ['question' => 'Which factor makes AI scams more effective?', 'answers' => [
                ['text' => 'Random guessing', 'correct' => false, 'explanation' => 'Incorrect: AI uses data, not random guessing.'],
                ['text' => 'Personalization using public information', 'correct' => true, 'explanation' => 'Correct: Personalized details increase trust and reduce suspicion.'],
                ['text' => 'Longer passwords', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
                ['text' => 'Slower internet', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Which is an example of a deepfake-enabled fraud scenario?', 'answers' => [
                ['text' => 'A routine campus announcement', 'correct' => false, 'explanation' => 'Incorrect: Normal communication.'],
                ['text' => 'A voice call that sounds like your manager requesting an urgent transfer', 'correct' => true, 'explanation' => 'Correct: Voice cloning can imitate a known authority figure to pressure payment.'],
                ['text' => 'A scheduled Zoom class', 'correct' => false, 'explanation' => 'Incorrect: Normal activity.'],
                ['text' => 'An app update notice', 'correct' => false, 'explanation' => 'Incorrect: Normal system activity.'],
            ]],
            ['question' => '"Seeing a familiar face on video" is enough to confirm identity.', 'answers' => [
                ['text' => 'True', 'correct' => false, 'explanation' => 'Incorrect: Deepfakes can imitate faces.'],
                ['text' => 'False', 'correct' => true, 'explanation' => 'Correct: Deepfake video can convincingly imitate faces, so verification must be independent.'],
            ]],
            ['question' => 'Which request should immediately raise suspicion in an AI/deepfake context?', 'answers' => [
                ['text' => 'A request to follow normal approval steps', 'correct' => false, 'explanation' => 'Incorrect: Following procedures is safe.'],
                ['text' => 'A request for an OTP or password', 'correct' => true, 'explanation' => 'Correct: Legitimate parties should not ask for OTPs/passwords through calls or chat.'],
                ['text' => 'A request to check the official portal', 'correct' => false, 'explanation' => 'Incorrect: Checking official portals is safe.'],
                ['text' => 'A reminder of a scheduled meeting', 'correct' => false, 'explanation' => 'Incorrect: Normal communication.'],
            ]],
            ['question' => 'The best verification method after a suspicious voice call is to:', 'answers' => [
                ['text' => 'Call the number that called you back', 'correct' => false, 'explanation' => 'Incorrect: May still be attacker-controlled.'],
                ['text' => 'Continue the call until you feel sure', 'correct' => false, 'explanation' => 'Incorrect: Prolongs exposure to manipulation.'],
                ['text' => 'Hang up and call the official/saved number yourself', 'correct' => true, 'explanation' => 'Correct: Call-back via a known number breaks attacker control.'],
                ['text' => 'Reply by SMS to confirm', 'correct' => false, 'explanation' => 'Incorrect: SMS can also be compromised.'],
            ]],
            ['question' => 'Why do scammers use urgency ("do this now") with deepfakes?', 'answers' => [
                ['text' => 'To give you time to verify', 'correct' => false, 'explanation' => 'Incorrect: Opposite intent.'],
                ['text' => 'To prevent you from verifying independently', 'correct' => true, 'explanation' => 'Correct: Urgency pushes fast action and bypasses verification steps.'],
                ['text' => 'To improve audio quality', 'correct' => false, 'explanation' => 'Incorrect: Unrelated to urgency.'],
                ['text' => 'To reduce costs', 'correct' => false, 'explanation' => 'Incorrect: Not the reason.'],
            ]],
            ['question' => 'Which is a good "process-based" defense against deepfakes?', 'answers' => [
                ['text' => 'Trusting voice recognition', 'correct' => false, 'explanation' => 'Incorrect: Voice can be cloned.'],
                ['text' => 'Trusting video quality', 'correct' => false, 'explanation' => 'Incorrect: Video can be faked.'],
                ['text' => 'Using a pre-agreed verification code word', 'correct' => true, 'explanation' => 'Correct: Shared passphrases/codes help confirm identity beyond media appearance.'],
                ['text' => 'Replying immediately to avoid escalation', 'correct' => false, 'explanation' => 'Incorrect: Speed increases risk.'],
            ]],
            ['question' => 'AI chatbots in scams often pretend to be:', 'answers' => [
                ['text' => 'Game characters', 'correct' => false, 'explanation' => 'Incorrect: Not common.'],
                ['text' => 'IT support or customer service agents', 'correct' => true, 'explanation' => 'Correct: Support roles carry authority and can request verification details.'],
                ['text' => 'Weather forecasters', 'correct' => false, 'explanation' => 'Incorrect: Not common.'],
                ['text' => 'Antivirus software', 'correct' => false, 'explanation' => 'Incorrect: Software doesn\'t chat.'],
            ]],
            ['question' => 'Which data source is commonly used to personalize AI scams?', 'answers' => [
                ['text' => 'Public social media posts and profiles', 'correct' => true, 'explanation' => 'Correct: Public posts can reveal roles, schedules, and contacts for tailoring messages.'],
                ['text' => 'Random number generators', 'correct' => false, 'explanation' => 'Incorrect: Not personalized.'],
                ['text' => 'Printer settings', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
                ['text' => 'Offline textbooks', 'correct' => false, 'explanation' => 'Incorrect: Not accessible to attackers.'],
            ]],
            ['question' => 'A deepfake attack is often combined with other channels to:', 'answers' => [
                ['text' => 'Make the scam less believable', 'correct' => false, 'explanation' => 'Incorrect: Opposite intent.'],
                ['text' => 'Add pressure and reduce doubt', 'correct' => true, 'explanation' => 'Correct: Multi-channel pressure (email + call + video) increases compliance likelihood.'],
                ['text' => 'Improve device performance', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
                ['text' => 'Replace MFA', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Which is a common red flag even when the video/audio looks real?', 'answers' => [
                ['text' => 'The request follows normal procedure', 'correct' => false, 'explanation' => 'Incorrect: Following procedures is safe.'],
                ['text' => 'The sender encourages you to verify', 'correct' => false, 'explanation' => 'Incorrect: Verification is good.'],
                ['text' => 'The request bypasses approvals and demands secrecy', 'correct' => true, 'explanation' => 'Correct: Breaking normal controls and discouraging verification is a major warning sign.'],
                ['text' => 'The message uses official portal links you already saved', 'correct' => false, 'explanation' => 'Incorrect: Using known links is safe.'],
            ]],
            ['question' => 'What should you preserve as evidence when reporting an AI-enhanced scam?', 'answers' => [
                ['text' => 'Only your feelings about it', 'correct' => false, 'explanation' => 'Incorrect: Need tangible evidence.'],
                ['text' => 'Screenshots, sender details, and call logs', 'correct' => true, 'explanation' => 'Correct: Evidence helps responders investigate patterns and block future attempts.'],
                ['text' => 'A public post about the attacker', 'correct' => false, 'explanation' => 'Incorrect: May cause issues.'],
                ['text' => 'Nothing—delete immediately', 'correct' => false, 'explanation' => 'Incorrect: Evidence is needed.'],
            ]],
            ['question' => 'Which statement best explains why "voice familiarity" is risky today?', 'answers' => [
                ['text' => 'Voices never change', 'correct' => false, 'explanation' => 'Incorrect: Not the issue.'],
                ['text' => 'AI can clone voices from short recordings', 'correct' => true, 'explanation' => 'Correct: Voice cloning can mimic a person using minimal sample audio.'],
                ['text' => 'Phones always distort voices beyond recognition', 'correct' => false, 'explanation' => 'Incorrect: Not true.'],
                ['text' => 'Caller ID guarantees identity', 'correct' => false, 'explanation' => 'Incorrect: Caller ID can be spoofed.'],
            ]],
            ['question' => 'If you receive a video message from a "boss" asking for urgent payment, you should:', 'answers' => [
                ['text' => 'Pay quickly to avoid trouble', 'correct' => false, 'explanation' => 'Incorrect: Never act on urgency alone.'],
                ['text' => 'Ask the video sender for their OTP', 'correct' => false, 'explanation' => 'Incorrect: Inappropriate request.'],
                ['text' => 'Verify using an official directory and normal approval steps', 'correct' => true, 'explanation' => 'Correct: Verification and normal process prevent fraud even when media looks real.'],
                ['text' => 'Forward the message to everyone immediately', 'correct' => false, 'explanation' => 'Incorrect: May spread panic.'],
            ]],
            ['question' => 'Why is rapid reporting especially important for AI-enhanced scams?', 'answers' => [
                ['text' => 'It makes the scam go viral', 'correct' => false, 'explanation' => 'Incorrect: Opposite intent.'],
                ['text' => 'It helps detect patterns and warn others quickly', 'correct' => true, 'explanation' => 'Correct: Early reports help organizations respond before more victims are targeted.'],
                ['text' => 'It permanently deletes evidence', 'correct' => false, 'explanation' => 'Incorrect: Reporting preserves evidence.'],
                ['text' => 'It replaces passwords', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Which behavior most increases risk in a deepfake scenario?', 'answers' => [
                ['text' => 'Pausing to verify', 'correct' => false, 'explanation' => 'Incorrect: Pausing is protective.'],
                ['text' => 'Acting immediately because the voice/video seems real', 'correct' => true, 'explanation' => 'Correct: Trusting media alone and acting fast is exactly what attackers want.'],
                ['text' => 'Using a call-back procedure', 'correct' => false, 'explanation' => 'Incorrect: Call-back is protective.'],
                ['text' => 'Following approval workflows', 'correct' => false, 'explanation' => 'Incorrect: Following workflows is protective.'],
            ]],
            ['question' => 'The best overall defense against AI-enhanced scams is to:', 'answers' => [
                ['text' => 'Trust realistic media', 'correct' => false, 'explanation' => 'Incorrect: Media can be faked.'],
                ['text' => 'Verify identity through trusted, independent channels and processes', 'correct' => true, 'explanation' => 'Correct: Process-based verification defeats impersonation even when media is convincing.'],
                ['text' => 'Respond faster than the attacker', 'correct' => false, 'explanation' => 'Incorrect: Speed increases risk.'],
                ['text' => 'Avoid all online communication', 'correct' => false, 'explanation' => 'Incorrect: Not practical.'],
            ]],
        ];
    }

    private function getLesson7Questions(): array
    {
        return [
            ['question' => 'What is pretexting?', 'answers' => [
                ['text' => 'A random phishing email', 'correct' => false, 'explanation' => 'Incorrect: Pretexting is more targeted.'],
                ['text' => 'Creating a false story to gain trust', 'correct' => true, 'explanation' => 'Correct: Pretexting relies on believable stories to manipulate victims.'],
                ['text' => 'A malware infection', 'correct' => false, 'explanation' => 'Incorrect: Technical attack, not pretexting.'],
                ['text' => 'A system failure', 'correct' => false, 'explanation' => 'Incorrect: Unrelated to pretexting.'],
            ]],
            ['question' => 'Pretexting attacks often succeed because they:', 'answers' => [
                ['text' => 'Use viruses', 'correct' => false, 'explanation' => 'Incorrect: Not virus-based.'],
                ['text' => 'Exploit trust and authority', 'correct' => true, 'explanation' => 'Correct: Trust and authority make the story believable.'],
                ['text' => 'Are very short', 'correct' => false, 'explanation' => 'Incorrect: Length doesn\'t determine success.'],
                ['text' => 'Avoid communication', 'correct' => false, 'explanation' => 'Incorrect: Pretexting requires communication.'],
            ]],
            ['question' => 'Which role is commonly impersonated in pretexting?', 'answers' => [
                ['text' => 'Movie star', 'correct' => false, 'explanation' => 'Incorrect: Not common.'],
                ['text' => 'IT support staff', 'correct' => true, 'explanation' => 'Correct: IT staff have authority to request access or information.'],
                ['text' => 'Game developer', 'correct' => false, 'explanation' => 'Incorrect: Not common.'],
                ['text' => 'Music artist', 'correct' => false, 'explanation' => 'Incorrect: Not common.'],
            ]],
            ['question' => 'What is baiting?', 'answers' => [
                ['text' => 'Threatening victims', 'correct' => false, 'explanation' => 'Incorrect: Baiting uses attraction, not threats.'],
                ['text' => 'Offering something attractive to lure victims', 'correct' => true, 'explanation' => 'Correct: Baiting uses incentives to trigger risky behavior.'],
                ['text' => 'Blocking accounts', 'correct' => false, 'explanation' => 'Incorrect: Not baiting.'],
                ['text' => 'Monitoring networks', 'correct' => false, 'explanation' => 'Incorrect: Not baiting.'],
            ]],
            ['question' => 'Which is an example of baiting?', 'answers' => [
                ['text' => 'Fake audit call', 'correct' => false, 'explanation' => 'Incorrect: That\'s pretexting.'],
                ['text' => 'Free software download link that installs malware', 'correct' => true, 'explanation' => 'Correct: Free offers are common baiting tactics.'],
                ['text' => 'Password reset notice', 'correct' => false, 'explanation' => 'Incorrect: That\'s phishing.'],
                ['text' => 'Policy reminder', 'correct' => false, 'explanation' => 'Incorrect: Normal communication.'],
            ]],
            ['question' => 'Physical baiting often involves:', 'answers' => [
                ['text' => 'Emails', 'correct' => false, 'explanation' => 'Incorrect: Email is digital.'],
                ['text' => 'Phone calls', 'correct' => false, 'explanation' => 'Incorrect: Calls are voice-based.'],
                ['text' => 'USB drives left in public places', 'correct' => true, 'explanation' => 'Correct: Infected USB devices are a known physical baiting method.'],
                ['text' => 'Video calls', 'correct' => false, 'explanation' => 'Incorrect: Video calls are digital.'],
            ]],
            ['question' => 'Why do attackers start with small requests?', 'answers' => [
                ['text' => 'To save time', 'correct' => false, 'explanation' => 'Incorrect: Not the reason.'],
                ['text' => 'To build trust and consistency', 'correct' => true, 'explanation' => 'Correct: Small requests lower resistance to larger ones later.'],
                ['text' => 'To confuse victims', 'correct' => false, 'explanation' => 'Incorrect: Not the primary tactic.'],
                ['text' => 'To avoid detection', 'correct' => false, 'explanation' => 'Incorrect: Not the primary reason.'],
            ]],
            ['question' => 'Which psychological tactic uses limited-time pressure?', 'answers' => [
                ['text' => 'Familiarity', 'correct' => false, 'explanation' => 'Incorrect: About recognition.'],
                ['text' => 'Authority', 'correct' => false, 'explanation' => 'Incorrect: About power.'],
                ['text' => 'Scarcity', 'correct' => true, 'explanation' => 'Correct: Scarcity creates urgency and pressure.'],
                ['text' => 'Empathy', 'correct' => false, 'explanation' => 'Incorrect: About emotional connection.'],
            ]],
            ['question' => 'Familiarity manipulation works by:', 'answers' => [
                ['text' => 'Using malware', 'correct' => false, 'explanation' => 'Incorrect: Not technical.'],
                ['text' => 'Referencing known people or events', 'correct' => true, 'explanation' => 'Correct: Familiar references reduce suspicion.'],
                ['text' => 'Sending attachments', 'correct' => false, 'explanation' => 'Incorrect: Not specific to familiarity.'],
                ['text' => 'Disabling MFA', 'correct' => false, 'explanation' => 'Incorrect: Technical action.'],
            ]],
            ['question' => 'Fear-based manipulation often includes:', 'answers' => [
                ['text' => 'Rewards', 'correct' => false, 'explanation' => 'Incorrect: That\'s reciprocity.'],
                ['text' => 'Threats of consequences', 'correct' => true, 'explanation' => 'Correct: Fear pressures victims to comply quickly.'],
                ['text' => 'Friendly greetings', 'correct' => false, 'explanation' => 'Incorrect: Not fear-based.'],
                ['text' => 'Discounts', 'correct' => false, 'explanation' => 'Incorrect: That\'s an incentive.'],
            ]],
            ['question' => 'Which behavior increases risk of pretexting success?', 'answers' => [
                ['text' => 'Verifying identity', 'correct' => false, 'explanation' => 'Incorrect: Verification protects.'],
                ['text' => 'Following procedures', 'correct' => false, 'explanation' => 'Incorrect: Procedures protect.'],
                ['text' => 'Skipping verification under pressure', 'correct' => true, 'explanation' => 'Correct: Ignoring verification enables manipulation.'],
                ['text' => 'Reporting incidents', 'correct' => false, 'explanation' => 'Incorrect: Reporting protects.'],
            ]],
            ['question' => 'Unknown USB devices should be:', 'answers' => [
                ['text' => 'Plugged in carefully', 'correct' => false, 'explanation' => 'Incorrect: Any plugging is risky.'],
                ['text' => 'Tested at home', 'correct' => false, 'explanation' => 'Incorrect: Still risky.'],
                ['text' => 'Avoided completely', 'correct' => true, 'explanation' => 'Correct: Unknown devices may contain malware.'],
                ['text' => 'Shared with others', 'correct' => false, 'explanation' => 'Incorrect: Spreads risk.'],
            ]],
            ['question' => 'Why is baiting effective?', 'answers' => [
                ['text' => 'It uses encryption', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
                ['text' => 'It exploits curiosity or greed', 'correct' => true, 'explanation' => 'Correct: Curiosity and desire override caution.'],
                ['text' => 'It blocks firewalls', 'correct' => false, 'explanation' => 'Incorrect: Technical claim.'],
                ['text' => 'It speeds systems', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Which defense best stops pretexting attacks?', 'answers' => [
                ['text' => 'Faster responses', 'correct' => false, 'explanation' => 'Incorrect: Speed increases risk.'],
                ['text' => 'Strict identity verification procedures', 'correct' => true, 'explanation' => 'Correct: Verification breaks the false story.'],
                ['text' => 'Trusting authority', 'correct' => false, 'explanation' => 'Incorrect: Blind trust is risky.'],
                ['text' => 'Ignoring messages', 'correct' => false, 'explanation' => 'Incorrect: May miss legitimate communication.'],
            ]],
            ['question' => 'Requests that bypass normal procedures are often a sign of:', 'answers' => [
                ['text' => 'System upgrades', 'correct' => false, 'explanation' => 'Incorrect: Upgrades follow procedures.'],
                ['text' => 'Pretexting or manipulation', 'correct' => true, 'explanation' => 'Correct: Attackers try to override controls.'],
                ['text' => 'Legitimate urgency', 'correct' => false, 'explanation' => 'Incorrect: Legitimate requests follow procedures.'],
                ['text' => 'Training exercises', 'correct' => false, 'explanation' => 'Incorrect: Training is announced.'],
            ]],
            ['question' => 'Why should suspicious offers be reported?', 'answers' => [
                ['text' => 'To assign blame', 'correct' => false, 'explanation' => 'Incorrect: Not the purpose.'],
                ['text' => 'To help protect others', 'correct' => true, 'explanation' => 'Correct: Reporting helps stop repeated attacks.'],
                ['text' => 'To gain rewards', 'correct' => false, 'explanation' => 'Incorrect: Not the purpose.'],
                ['text' => 'To delete files', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Combining pretexting and baiting increases risk because it:', 'answers' => [
                ['text' => 'Reduces realism', 'correct' => false, 'explanation' => 'Incorrect: Opposite effect.'],
                ['text' => 'Uses multiple psychological triggers', 'correct' => true, 'explanation' => 'Correct: Multiple tactics increase effectiveness.'],
                ['text' => 'Slows attacks', 'correct' => false, 'explanation' => 'Incorrect: Doesn\'t slow attacks.'],
                ['text' => 'Requires malware', 'correct' => false, 'explanation' => 'Incorrect: Not necessarily.'],
            ]],
            ['question' => 'Which action best protects against manipulation?', 'answers' => [
                ['text' => 'Acting quickly', 'correct' => false, 'explanation' => 'Incorrect: Speed increases risk.'],
                ['text' => 'Verifying requests through trusted channels', 'correct' => true, 'explanation' => 'Correct: Independent verification prevents deception.'],
                ['text' => 'Trusting familiarity', 'correct' => false, 'explanation' => 'Incorrect: Familiarity can be faked.'],
                ['text' => 'Avoiding updates', 'correct' => false, 'explanation' => 'Incorrect: Updates improve security.'],
            ]],
            ['question' => 'Advanced manipulation often relies on:', 'answers' => [
                ['text' => 'Random messages', 'correct' => false, 'explanation' => 'Incorrect: Advanced attacks are targeted.'],
                ['text' => 'Careful planning and research', 'correct' => true, 'explanation' => 'Correct: Preparation makes scams believable.'],
                ['text' => 'Poor grammar', 'correct' => false, 'explanation' => 'Incorrect: Advanced scams avoid this.'],
                ['text' => 'Short timelines', 'correct' => false, 'explanation' => 'Incorrect: Not the primary factor.'],
            ]],
            ['question' => 'The safest response to baiting attempts is to:', 'answers' => [
                ['text' => 'Investigate alone', 'correct' => false, 'explanation' => 'Incorrect: May trigger the bait.'],
                ['text' => 'Interact cautiously', 'correct' => false, 'explanation' => 'Incorrect: Any interaction is risky.'],
                ['text' => 'Avoid interaction and report', 'correct' => true, 'explanation' => 'Correct: Avoiding interaction prevents compromise.'],
                ['text' => 'Share the offer', 'correct' => false, 'explanation' => 'Incorrect: Spreads the threat.'],
            ]],
        ];
    }

    private function getLesson8Questions(): array
    {
        return [
            ['question' => 'What is RA 10175 also known as?', 'answers' => [
                ['text' => 'Data Privacy Act', 'correct' => false, 'explanation' => 'Incorrect: Different law.'],
                ['text' => 'Cybercrime Prevention Act of 2012', 'correct' => true, 'explanation' => 'Correct: RA 10175 is officially the Cybercrime Prevention Act of 2012.'],
                ['text' => 'Anti-Hacking Law', 'correct' => false, 'explanation' => 'Incorrect: Informal name.'],
                ['text' => 'Internet Safety Act', 'correct' => false, 'explanation' => 'Incorrect: Different law.'],
            ]],
            ['question' => 'RA 10175 primarily addresses crimes committed using:', 'answers' => [
                ['text' => 'Printed documents', 'correct' => false, 'explanation' => 'Incorrect: Physical documents.'],
                ['text' => 'Television', 'correct' => false, 'explanation' => 'Incorrect: Broadcast media.'],
                ['text' => 'Computers and the internet', 'correct' => true, 'explanation' => 'Correct: The law covers crimes involving ICT systems.'],
                ['text' => 'Postal mail', 'correct' => false, 'explanation' => 'Incorrect: Physical mail.'],
            ]],
            ['question' => 'Which is an offense against system integrity?', 'answers' => [
                ['text' => 'Online shopping', 'correct' => false, 'explanation' => 'Incorrect: Legal activity.'],
                ['text' => 'System interference', 'correct' => true, 'explanation' => 'Correct: System interference disrupts system operations.'],
                ['text' => 'Legal access', 'correct' => false, 'explanation' => 'Incorrect: Legal activity.'],
                ['text' => 'Software updates', 'correct' => false, 'explanation' => 'Incorrect: Maintenance activity.'],
            ]],
            ['question' => 'Illegal access means:', 'answers' => [
                ['text' => 'Using public Wi-Fi', 'correct' => false, 'explanation' => 'Incorrect: Legal activity.'],
                ['text' => 'Accessing systems without authorization', 'correct' => true, 'explanation' => 'Correct: Unauthorized access is illegal even without data theft.'],
                ['text' => 'Forgetting passwords', 'correct' => false, 'explanation' => 'Incorrect: User error.'],
                ['text' => 'Updating software', 'correct' => false, 'explanation' => 'Incorrect: Maintenance activity.'],
            ]],
            ['question' => 'Hacking an account "just to look" is:', 'answers' => [
                ['text' => 'Allowed', 'correct' => false, 'explanation' => 'Incorrect: Still illegal.'],
                ['text' => 'Legal', 'correct' => false, 'explanation' => 'Incorrect: Still illegal.'],
                ['text' => 'Still a crime under RA 10175', 'correct' => true, 'explanation' => 'Correct: Intentional unauthorized access is punishable.'],
                ['text' => 'Encouraged', 'correct' => false, 'explanation' => 'Incorrect: Illegal activity.'],
            ]],
            ['question' => 'Cyber libel differs from traditional libel because it:', 'answers' => [
                ['text' => 'Has lower penalties', 'correct' => false, 'explanation' => 'Incorrect: Has higher penalties.'],
                ['text' => 'Is spoken', 'correct' => false, 'explanation' => 'Incorrect: Written online.'],
                ['text' => 'Uses computer systems and has wider reach', 'correct' => true, 'explanation' => 'Correct: Online content spreads faster and wider.'],
                ['text' => 'Is anonymous', 'correct' => false, 'explanation' => 'Incorrect: Can be traced.'],
            ]],
            ['question' => 'Posting defamatory content online may result in:', 'answers' => [
                ['text' => 'No consequence', 'correct' => false, 'explanation' => 'Incorrect: Has legal consequences.'],
                ['text' => 'Account suspension only', 'correct' => false, 'explanation' => 'Incorrect: Can have criminal liability.'],
                ['text' => 'Criminal liability under RA 10175', 'correct' => true, 'explanation' => 'Correct: Cyber libel is punishable under the law.'],
                ['text' => 'Free speech protection always', 'correct' => false, 'explanation' => 'Incorrect: Free speech has limits.'],
            ]],
            ['question' => 'Identity theft involves:', 'answers' => [
                ['text' => 'Forgetting an ID', 'correct' => false, 'explanation' => 'Incorrect: User error.'],
                ['text' => 'Using another person\'s identity without permission', 'correct' => true, 'explanation' => 'Correct: Unauthorized use of identity is illegal.'],
                ['text' => 'Sharing your own data', 'correct' => false, 'explanation' => 'Incorrect: Personal choice.'],
                ['text' => 'Password recovery', 'correct' => false, 'explanation' => 'Incorrect: Legitimate process.'],
            ]],
            ['question' => 'Online fraud includes:', 'answers' => [
                ['text' => 'Legal online sales', 'correct' => false, 'explanation' => 'Incorrect: Legal commerce.'],
                ['text' => 'Phishing scams', 'correct' => true, 'explanation' => 'Correct: Phishing is a form of online fraud.'],
                ['text' => 'Software licensing', 'correct' => false, 'explanation' => 'Incorrect: Legal activity.'],
                ['text' => 'Online banking', 'correct' => false, 'explanation' => 'Incorrect: Legal activity.'],
            ]],
            ['question' => 'Which category includes computer-related fraud?', 'answers' => [
                ['text' => 'Content-related offenses', 'correct' => false, 'explanation' => 'Incorrect: Different category.'],
                ['text' => 'Computer-related offenses', 'correct' => true, 'explanation' => 'Correct: Fraud using computers falls under this category.'],
                ['text' => 'System maintenance', 'correct' => false, 'explanation' => 'Incorrect: Not an offense.'],
                ['text' => 'Data privacy only', 'correct' => false, 'explanation' => 'Incorrect: Different law.'],
            ]],
            ['question' => 'RA 10175 penalties may include:', 'answers' => [
                ['text' => 'Warnings only', 'correct' => false, 'explanation' => 'Incorrect: More severe.'],
                ['text' => 'Community service', 'correct' => false, 'explanation' => 'Incorrect: Not the primary penalty.'],
                ['text' => 'Imprisonment and fines', 'correct' => true, 'explanation' => 'Correct: Serious offenses carry criminal penalties.'],
                ['text' => 'Internet ban only', 'correct' => false, 'explanation' => 'Incorrect: Not the penalty.'],
            ]],
            ['question' => 'Crimes using ICT may receive:', 'answers' => [
                ['text' => 'Lighter penalties', 'correct' => false, 'explanation' => 'Incorrect: Opposite effect.'],
                ['text' => 'No penalty', 'correct' => false, 'explanation' => 'Incorrect: Punishable.'],
                ['text' => 'Higher penalties than offline crimes', 'correct' => true, 'explanation' => 'Correct: ICT-based crimes are often penalized one degree higher.'],
                ['text' => 'Civil penalties only', 'correct' => false, 'explanation' => 'Incorrect: Criminal penalties apply.'],
            ]],
            ['question' => 'RA 10175 can apply to crimes committed outside the Philippines if:', 'answers' => [
                ['text' => 'The offender is anonymous', 'correct' => false, 'explanation' => 'Incorrect: Not the criterion.'],
                ['text' => 'Philippine systems or citizens are affected', 'correct' => true, 'explanation' => 'Correct: The law has extraterritorial application in some cases.'],
                ['text' => 'The crime is small', 'correct' => false, 'explanation' => 'Incorrect: Severity doesn\'t exempt.'],
                ['text' => 'The offender apologizes', 'correct' => false, 'explanation' => 'Incorrect: Doesn\'t negate liability.'],
            ]],
            ['question' => 'Which action helps avoid violating RA 10175?', 'answers' => [
                ['text' => 'Sharing credentials', 'correct' => false, 'explanation' => 'Incorrect: Risky behavior.'],
                ['text' => 'Accessing unauthorized systems', 'correct' => false, 'explanation' => 'Incorrect: Illegal.'],
                ['text' => 'Using technology responsibly', 'correct' => true, 'explanation' => 'Correct: Ethical use prevents legal issues.'],
                ['text' => 'Posting without thinking', 'correct' => false, 'explanation' => 'Incorrect: May lead to violations.'],
            ]],
            ['question' => 'Accessing a classmate\'s account without permission is:', 'answers' => [
                ['text' => 'Acceptable', 'correct' => false, 'explanation' => 'Incorrect: Illegal.'],
                ['text' => 'A joke', 'correct' => false, 'explanation' => 'Incorrect: Still illegal.'],
                ['text' => 'Illegal access under RA 10175', 'correct' => true, 'explanation' => 'Correct: Unauthorized access is punishable.'],
                ['text' => 'Not covered', 'correct' => false, 'explanation' => 'Incorrect: Covered by the law.'],
            ]],
            ['question' => 'Which is a content-related offense?', 'answers' => [
                ['text' => 'Cyber libel', 'correct' => true, 'explanation' => 'Correct: Cyber libel involves online content.'],
                ['text' => 'System update', 'correct' => false, 'explanation' => 'Incorrect: Maintenance activity.'],
                ['text' => 'File backup', 'correct' => false, 'explanation' => 'Incorrect: Data management.'],
                ['text' => 'Password change', 'correct' => false, 'explanation' => 'Incorrect: Security practice.'],
            ]],
            ['question' => 'Responsible online behavior includes:', 'answers' => [
                ['text' => 'Posting harmful content', 'correct' => false, 'explanation' => 'Incorrect: Irresponsible.'],
                ['text' => 'Respecting laws and digital rights', 'correct' => true, 'explanation' => 'Correct: Legal awareness supports safe use.'],
                ['text' => 'Bypassing security', 'correct' => false, 'explanation' => 'Incorrect: Illegal.'],
                ['text' => 'Sharing fake news', 'correct' => false, 'explanation' => 'Incorrect: Irresponsible.'],
            ]],
            ['question' => 'Why should users understand cybercrime laws?', 'answers' => [
                ['text' => 'To avoid updates', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
                ['text' => 'To prevent accidental violations', 'correct' => true, 'explanation' => 'Correct: Awareness reduces legal risk.'],
                ['text' => 'To bypass systems', 'correct' => false, 'explanation' => 'Incorrect: Illegal intent.'],
                ['text' => 'To test limits', 'correct' => false, 'explanation' => 'Incorrect: Risky behavior.'],
            ]],
            ['question' => 'Reporting cybercrime helps by:', 'answers' => [
                ['text' => 'Hiding incidents', 'correct' => false, 'explanation' => 'Incorrect: Opposite effect.'],
                ['text' => 'Allowing authorities to investigate', 'correct' => true, 'explanation' => 'Correct: Reporting supports enforcement.'],
                ['text' => 'Delaying response', 'correct' => false, 'explanation' => 'Incorrect: Opposite effect.'],
                ['text' => 'Encouraging scams', 'correct' => false, 'explanation' => 'Incorrect: Opposite effect.'],
            ]],
            ['question' => 'RA 10175 promotes safe internet use by:', 'answers' => [
                ['text' => 'Limiting access', 'correct' => false, 'explanation' => 'Incorrect: Doesn\'t limit access.'],
                ['text' => 'Penalizing cybercrime and encouraging responsibility', 'correct' => true, 'explanation' => 'Correct: The law deters cybercrime and promotes accountability.'],
                ['text' => 'Removing privacy', 'correct' => false, 'explanation' => 'Incorrect: Privacy is protected.'],
                ['text' => 'Blocking all content', 'correct' => false, 'explanation' => 'Incorrect: Doesn\'t block content.'],
            ]],
        ];
    }

    private function getLesson9Questions(): array
    {
        return [
            ['question' => 'Why is reporting cyber incidents important?', 'answers' => [
                ['text' => 'To assign blame', 'correct' => false, 'explanation' => 'Incorrect: Not the purpose.'],
                ['text' => 'To stop attacks and protect others', 'correct' => true, 'explanation' => 'Correct: Reporting helps contain threats and prevent repeat attacks.'],
                ['text' => 'To delete evidence', 'correct' => false, 'explanation' => 'Incorrect: Evidence should be preserved.'],
                ['text' => 'To slow investigations', 'correct' => false, 'explanation' => 'Incorrect: Opposite effect.'],
            ]],
            ['question' => 'Which group investigates cybercrime in the Philippines?', 'answers' => [
                ['text' => 'Bureau of Customs', 'correct' => false, 'explanation' => 'Incorrect: Different mandate.'],
                ['text' => 'PNP Anti-Cybercrime Group (PNP-ACG)', 'correct' => true, 'explanation' => 'Correct: PNP-ACG handles cybercrime investigations.'],
                ['text' => 'Department of Tourism', 'correct' => false, 'explanation' => 'Incorrect: Different mandate.'],
                ['text' => 'Commission on Audit', 'correct' => false, 'explanation' => 'Incorrect: Different mandate.'],
            ]],
            ['question' => 'NBI-CCD stands for:', 'answers' => [
                ['text' => 'National Banking Investigation', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'National Bureau of Investigation Cybercrime Division', 'correct' => true, 'explanation' => 'Correct: NBI-CCD is the cybercrime unit of the NBI.'],
                ['text' => 'Network Business Intelligence Center', 'correct' => false, 'explanation' => 'Incorrect.'],
                ['text' => 'National Broadband Infrastructure', 'correct' => false, 'explanation' => 'Incorrect.'],
            ]],
            ['question' => 'Which incident should be reported to internal IT first?', 'answers' => [
                ['text' => 'Public scam', 'correct' => false, 'explanation' => 'Incorrect: May need law enforcement.'],
                ['text' => 'Workplace account compromise', 'correct' => true, 'explanation' => 'Correct: Internal incidents should follow organizational procedures.'],
                ['text' => 'Overseas crime', 'correct' => false, 'explanation' => 'Incorrect: Requires law enforcement.'],
                ['text' => 'Spam ads', 'correct' => false, 'explanation' => 'Incorrect: Minor issue.'],
            ]],
            ['question' => 'What type of information strengthens a cybercrime report?', 'answers' => [
                ['text' => 'Opinions', 'correct' => false, 'explanation' => 'Incorrect: Need facts.'],
                ['text' => 'Screenshots and logs', 'correct' => true, 'explanation' => 'Correct: Evidence supports investigation.'],
                ['text' => 'Rumors', 'correct' => false, 'explanation' => 'Incorrect: Unreliable.'],
                ['text' => 'Edited messages', 'correct' => false, 'explanation' => 'Incorrect: Evidence should be original.'],
            ]],
            ['question' => 'Why should evidence not be deleted immediately?', 'answers' => [
                ['text' => 'It uses storage', 'correct' => false, 'explanation' => 'Incorrect: Storage isn\'t the concern.'],
                ['text' => 'It may be needed for investigation', 'correct' => true, 'explanation' => 'Correct: Preserving evidence ensures proper investigation.'],
                ['text' => 'It slows systems', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
                ['text' => 'It blocks accounts', 'correct' => false, 'explanation' => 'Incorrect: Doesn\'t block accounts.'],
            ]],
            ['question' => 'Which detail is important when reporting an incident?', 'answers' => [
                ['text' => 'Favorite apps', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
                ['text' => 'Date and time of occurrence', 'correct' => true, 'explanation' => 'Correct: Timing helps establish event timelines.'],
                ['text' => 'Personal opinions', 'correct' => false, 'explanation' => 'Incorrect: Need facts.'],
                ['text' => 'Device wallpaper', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Platform providers should be notified because they can:', 'answers' => [
                ['text' => 'Ignore reports', 'correct' => false, 'explanation' => 'Incorrect: They take action.'],
                ['text' => 'Suspend attacker accounts and assist investigations', 'correct' => true, 'explanation' => 'Correct: Providers can take corrective action.'],
                ['text' => 'Delete evidence', 'correct' => false, 'explanation' => 'Incorrect: They preserve evidence.'],
                ['text' => 'Block reporters', 'correct' => false, 'explanation' => 'Incorrect: They protect reporters.'],
            ]],
            ['question' => 'Law enforcement investigations must follow legal procedures to:', 'answers' => [
                ['text' => 'Delay cases', 'correct' => false, 'explanation' => 'Incorrect: Procedures ensure efficiency.'],
                ['text' => 'Ensure evidence is admissible in court', 'correct' => true, 'explanation' => 'Correct: Legal compliance is required for prosecution.'],
                ['text' => 'Avoid reporting', 'correct' => false, 'explanation' => 'Incorrect: Reporting is required.'],
                ['text' => 'Reduce workload', 'correct' => false, 'explanation' => 'Incorrect: Not the purpose.'],
            ]],
            ['question' => 'Which action may weaken a cybercrime case?', 'answers' => [
                ['text' => 'Preserving logs', 'correct' => false, 'explanation' => 'Incorrect: Strengthens case.'],
                ['text' => 'Reporting promptly', 'correct' => false, 'explanation' => 'Incorrect: Strengthens case.'],
                ['text' => 'Altering or deleting evidence', 'correct' => true, 'explanation' => 'Correct: Evidence tampering harms investigations.'],
                ['text' => 'Cooperating with authorities', 'correct' => false, 'explanation' => 'Incorrect: Strengthens case.'],
            ]],
            ['question' => 'Reporting helps organizations by:', 'answers' => [
                ['text' => 'Hiding weaknesses', 'correct' => false, 'explanation' => 'Incorrect: Reveals areas for improvement.'],
                ['text' => 'Improving defenses based on patterns', 'correct' => true, 'explanation' => 'Correct: Reports inform better security measures.'],
                ['text' => 'Punishing users', 'correct' => false, 'explanation' => 'Incorrect: Not the purpose.'],
                ['text' => 'Disabling systems', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Which agency handles national-level cyber investigations?', 'answers' => [
                ['text' => 'Barangay council', 'correct' => false, 'explanation' => 'Incorrect: Local government.'],
                ['text' => 'NBI Cybercrime Division (NBI-CCD)', 'correct' => true, 'explanation' => 'Correct: NBI-CCD handles serious cybercrime cases.'],
                ['text' => 'Local school office', 'correct' => false, 'explanation' => 'Incorrect: Educational institution.'],
                ['text' => 'Internet café', 'correct' => false, 'explanation' => 'Incorrect: Business establishment.'],
            ]],
            ['question' => 'What should be included in a cyber incident report?', 'answers' => [
                ['text' => 'Edited screenshots', 'correct' => false, 'explanation' => 'Incorrect: Should be original.'],
                ['text' => 'Original messages and records', 'correct' => true, 'explanation' => 'Correct: Original data preserves integrity.'],
                ['text' => 'Assumptions', 'correct' => false, 'explanation' => 'Incorrect: Need facts.'],
                ['text' => 'Social media comments', 'correct' => false, 'explanation' => 'Incorrect: May not be reliable.'],
            ]],
            ['question' => 'Cooperation with investigators means:', 'answers' => [
                ['text' => 'Ignoring requests', 'correct' => false, 'explanation' => 'Incorrect: Must cooperate.'],
                ['text' => 'Providing accurate information when requested', 'correct' => true, 'explanation' => 'Correct: Cooperation supports lawful investigation.'],
                ['text' => 'Sharing rumors', 'correct' => false, 'explanation' => 'Incorrect: Share facts only.'],
                ['text' => 'Deleting accounts', 'correct' => false, 'explanation' => 'Incorrect: Preserves evidence.'],
            ]],
            ['question' => 'Reporting scams can help because it:', 'answers' => [
                ['text' => 'Encourages attackers', 'correct' => false, 'explanation' => 'Incorrect: Opposite effect.'],
                ['text' => 'Helps warn others and stop repeat crimes', 'correct' => true, 'explanation' => 'Correct: Reporting protects the community.'],
                ['text' => 'Delays enforcement', 'correct' => false, 'explanation' => 'Incorrect: Speeds enforcement.'],
                ['text' => 'Reduces awareness', 'correct' => false, 'explanation' => 'Incorrect: Increases awareness.'],
            ]],
            ['question' => 'Which responsibility applies to all users?', 'answers' => [
                ['text' => 'Conduct investigations', 'correct' => false, 'explanation' => 'Incorrect: Law enforcement role.'],
                ['text' => 'Report suspicious activity promptly', 'correct' => true, 'explanation' => 'Correct: Reporting is everyone\'s responsibility.'],
                ['text' => 'Arrest suspects', 'correct' => false, 'explanation' => 'Incorrect: Law enforcement role.'],
                ['text' => 'Control networks', 'correct' => false, 'explanation' => 'Incorrect: IT role.'],
            ]],
            ['question' => 'What is the role of enforcement agencies?', 'answers' => [
                ['text' => 'Ignore minor cases', 'correct' => false, 'explanation' => 'Incorrect: All cases matter.'],
                ['text' => 'Investigate and prosecute cybercrime', 'correct' => true, 'explanation' => 'Correct: Enforcement ensures accountability.'],
                ['text' => 'Provide antivirus software', 'correct' => false, 'explanation' => 'Incorrect: Not their role.'],
                ['text' => 'Manage accounts', 'correct' => false, 'explanation' => 'Incorrect: Platform role.'],
            ]],
            ['question' => 'Digital evidence should be handled by:', 'answers' => [
                ['text' => 'Editing for clarity', 'correct' => false, 'explanation' => 'Incorrect: Keep original.'],
                ['text' => 'Preserving original form', 'correct' => true, 'explanation' => 'Correct: Original evidence maintains integrity.'],
                ['text' => 'Sharing publicly', 'correct' => false, 'explanation' => 'Incorrect: May compromise investigation.'],
                ['text' => 'Deleting after review', 'correct' => false, 'explanation' => 'Incorrect: Should preserve.'],
            ]],
            ['question' => 'Which situation requires law enforcement involvement?', 'answers' => [
                ['text' => 'Minor typo', 'correct' => false, 'explanation' => 'Incorrect: Not a crime.'],
                ['text' => 'Cyber fraud with financial loss', 'correct' => true, 'explanation' => 'Correct: Financial cybercrime warrants enforcement action.'],
                ['text' => 'App updates', 'correct' => false, 'explanation' => 'Incorrect: Normal activity.'],
                ['text' => 'Password change', 'correct' => false, 'explanation' => 'Incorrect: Normal activity.'],
            ]],
            ['question' => 'Effective reporting and enforcement ultimately help to:', 'answers' => [
                ['text' => 'Reduce trust', 'correct' => false, 'explanation' => 'Incorrect: Builds trust.'],
                ['text' => 'Strengthen cybersecurity and accountability', 'correct' => true, 'explanation' => 'Correct: Proper reporting improves overall security.'],
                ['text' => 'Increase crime', 'correct' => false, 'explanation' => 'Incorrect: Reduces crime.'],
                ['text' => 'Limit reporting', 'correct' => false, 'explanation' => 'Incorrect: Encourages reporting.'],
            ]],
        ];
    }

    private function getLesson10Questions(): array
    {
        return [
            ['question' => 'What is cyber hygiene?', 'answers' => [
                ['text' => 'Cleaning devices physically', 'correct' => false, 'explanation' => 'Incorrect: Not physical cleaning.'],
                ['text' => 'Daily practices that keep digital life secure', 'correct' => true, 'explanation' => 'Correct: Cyber hygiene refers to routine security habits.'],
                ['text' => 'Installing games', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
                ['text' => 'Blocking the internet', 'correct' => false, 'explanation' => 'Incorrect: Not about blocking.'],
            ]],
            ['question' => 'Why is cyber hygiene important?', 'answers' => [
                ['text' => 'It speeds up computers', 'correct' => false, 'explanation' => 'Incorrect: Not the primary purpose.'],
                ['text' => 'It reduces the risk of cyberattacks', 'correct' => true, 'explanation' => 'Correct: Good habits prevent many common attacks.'],
                ['text' => 'It removes all threats', 'correct' => false, 'explanation' => 'Incorrect: Cannot remove all threats.'],
                ['text' => 'It replaces antivirus software', 'correct' => false, 'explanation' => 'Incorrect: Complements, doesn\'t replace.'],
            ]],
            ['question' => 'Which password practice is recommended?', 'answers' => [
                ['text' => 'Reusing passwords', 'correct' => false, 'explanation' => 'Incorrect: Increases risk.'],
                ['text' => 'Short passwords', 'correct' => false, 'explanation' => 'Incorrect: Weak security.'],
                ['text' => 'Unique passwords for each account', 'correct' => true, 'explanation' => 'Correct: Unique passwords prevent account takeover.'],
                ['text' => 'Writing passwords publicly', 'correct' => false, 'explanation' => 'Incorrect: Security risk.'],
            ]],
            ['question' => 'Password managers are useful because they:', 'answers' => [
                ['text' => 'Share passwords', 'correct' => false, 'explanation' => 'Incorrect: Keep passwords private.'],
                ['text' => 'Store passwords securely and generate strong ones', 'correct' => true, 'explanation' => 'Correct: Password managers support strong password hygiene.'],
                ['text' => 'Disable MFA', 'correct' => false, 'explanation' => 'Incorrect: Work with MFA.'],
                ['text' => 'Remove encryption', 'correct' => false, 'explanation' => 'Incorrect: Use encryption.'],
            ]],
            ['question' => 'Multi-factor authentication helps by:', 'answers' => [
                ['text' => 'Removing passwords', 'correct' => false, 'explanation' => 'Incorrect: Adds to passwords.'],
                ['text' => 'Adding another verification step', 'correct' => true, 'explanation' => 'Correct: MFA reduces risk if passwords are stolen.'],
                ['text' => 'Slowing login', 'correct' => false, 'explanation' => 'Incorrect: Security benefit outweighs slight delay.'],
                ['text' => 'Blocking updates', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Which action improves device security?', 'answers' => [
                ['text' => 'Ignoring updates', 'correct' => false, 'explanation' => 'Incorrect: Updates are important.'],
                ['text' => 'Installing security patches promptly', 'correct' => true, 'explanation' => 'Correct: Updates fix known vulnerabilities.'],
                ['text' => 'Disabling locks', 'correct' => false, 'explanation' => 'Incorrect: Reduces security.'],
                ['text' => 'Using unknown apps', 'correct' => false, 'explanation' => 'Incorrect: Security risk.'],
            ]],
            ['question' => 'Locking your device helps because it:', 'answers' => [
                ['text' => 'Saves battery', 'correct' => false, 'explanation' => 'Incorrect: Not the security purpose.'],
                ['text' => 'Prevents unauthorized access', 'correct' => true, 'explanation' => 'Correct: Device locks protect stored data.'],
                ['text' => 'Improves Wi-Fi', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
                ['text' => 'Removes malware', 'correct' => false, 'explanation' => 'Incorrect: Doesn\'t remove malware.'],
            ]],
            ['question' => 'Public Wi-Fi is risky because it:', 'answers' => [
                ['text' => 'Is slow', 'correct' => false, 'explanation' => 'Incorrect: Speed isn\'t the security issue.'],
                ['text' => 'Is often unsecured and can be monitored by attackers', 'correct' => true, 'explanation' => 'Correct: Unsecured networks expose data.'],
                ['text' => 'Uses passwords', 'correct' => false, 'explanation' => 'Incorrect: Password doesn\'t guarantee security.'],
                ['text' => 'Requires login', 'correct' => false, 'explanation' => 'Incorrect: Login doesn\'t mean secure.'],
            ]],
            ['question' => 'Sensitive activities on public Wi-Fi should be:', 'answers' => [
                ['text' => 'Encouraged', 'correct' => false, 'explanation' => 'Incorrect: Very risky.'],
                ['text' => 'Avoided', 'correct' => true, 'explanation' => 'Correct: Public networks increase interception risk.'],
                ['text' => 'Done quickly', 'correct' => false, 'explanation' => 'Incorrect: Still risky.'],
                ['text' => 'Shared', 'correct' => false, 'explanation' => 'Incorrect: Increases risk.'],
            ]],
            ['question' => 'HTTPS indicates that a website:', 'answers' => [
                ['text' => 'Is fake', 'correct' => false, 'explanation' => 'Incorrect: HTTPS is legitimate.'],
                ['text' => 'Uses encrypted communication', 'correct' => true, 'explanation' => 'Correct: HTTPS encrypts data in transit.'],
                ['text' => 'Has no security', 'correct' => false, 'explanation' => 'Incorrect: Has encryption.'],
                ['text' => 'Is offline', 'correct' => false, 'explanation' => 'Incorrect: Must be online.'],
            ]],
            ['question' => 'Automatic connection to unknown networks should be:', 'answers' => [
                ['text' => 'Enabled', 'correct' => false, 'explanation' => 'Incorrect: Security risk.'],
                ['text' => 'Disabled', 'correct' => true, 'explanation' => 'Correct: Unknown networks can be malicious.'],
                ['text' => 'Ignored', 'correct' => false, 'explanation' => 'Incorrect: Should be disabled.'],
                ['text' => 'Required', 'correct' => false, 'explanation' => 'Incorrect: Should be disabled.'],
            ]],
            ['question' => 'Which habit helps detect account compromise early?', 'answers' => [
                ['text' => 'Ignoring notifications', 'correct' => false, 'explanation' => 'Incorrect: Should check notifications.'],
                ['text' => 'Monitoring account activity regularly', 'correct' => true, 'explanation' => 'Correct: Monitoring helps catch suspicious behavior early.'],
                ['text' => 'Sharing credentials', 'correct' => false, 'explanation' => 'Incorrect: Increases risk.'],
                ['text' => 'Disabling alerts', 'correct' => false, 'explanation' => 'Incorrect: Alerts are important.'],
            ]],
            ['question' => 'Clicking unknown links increases risk of:', 'answers' => [
                ['text' => 'Faster browsing', 'correct' => false, 'explanation' => 'Incorrect: Not the outcome.'],
                ['text' => 'Malware and phishing attacks', 'correct' => true, 'explanation' => 'Correct: Unknown links often deliver attacks.'],
                ['text' => 'Software updates', 'correct' => false, 'explanation' => 'Incorrect: Updates come from official sources.'],
                ['text' => 'Encryption', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Verifying requests for information helps prevent:', 'answers' => [
                ['text' => 'Updates', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
                ['text' => 'Social engineering scams', 'correct' => true, 'explanation' => 'Correct: Verification blocks manipulation.'],
                ['text' => 'Account creation', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
                ['text' => 'Encryption', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Installing apps only from trusted sources helps because it:', 'answers' => [
                ['text' => 'Limits features', 'correct' => false, 'explanation' => 'Incorrect: Not the purpose.'],
                ['text' => 'Reduces malware risk', 'correct' => true, 'explanation' => 'Correct: Trusted sources vet applications.'],
                ['text' => 'Slows devices', 'correct' => false, 'explanation' => 'Incorrect: Not the outcome.'],
                ['text' => 'Disables security', 'correct' => false, 'explanation' => 'Incorrect: Improves security.'],
            ]],
            ['question' => 'Which behavior weakens cyber hygiene?', 'answers' => [
                ['text' => 'Regular updates', 'correct' => false, 'explanation' => 'Incorrect: Strengthens security.'],
                ['text' => 'Password reuse across accounts', 'correct' => true, 'explanation' => 'Correct: Reused passwords amplify breaches.'],
                ['text' => 'MFA usage', 'correct' => false, 'explanation' => 'Incorrect: Strengthens security.'],
                ['text' => 'Device locking', 'correct' => false, 'explanation' => 'Incorrect: Strengthens security.'],
            ]],
            ['question' => 'Reporting suspicious activity is important because it:', 'answers' => [
                ['text' => 'Causes panic', 'correct' => false, 'explanation' => 'Incorrect: Enables response.'],
                ['text' => 'Helps stop attacks early', 'correct' => true, 'explanation' => 'Correct: Early reporting limits damage.'],
                ['text' => 'Slows systems', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
                ['text' => 'Deletes data', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Antivirus software helps by:', 'answers' => [
                ['text' => 'Creating viruses', 'correct' => false, 'explanation' => 'Incorrect: Opposite purpose.'],
                ['text' => 'Detecting and blocking malicious software', 'correct' => true, 'explanation' => 'Correct: Antivirus is a protective layer.'],
                ['text' => 'Replacing updates', 'correct' => false, 'explanation' => 'Incorrect: Complements updates.'],
                ['text' => 'Disabling MFA', 'correct' => false, 'explanation' => 'Incorrect: Works with MFA.'],
            ]],
            ['question' => 'Cyber hygiene focuses primarily on:', 'answers' => [
                ['text' => 'Advanced hacking skills', 'correct' => false, 'explanation' => 'Incorrect: Basic practices.'],
                ['text' => 'Everyday preventive practices', 'correct' => true, 'explanation' => 'Correct: Daily habits form the foundation of security.'],
                ['text' => 'Law enforcement', 'correct' => false, 'explanation' => 'Incorrect: Individual responsibility.'],
                ['text' => 'System administration', 'correct' => false, 'explanation' => 'Incorrect: User practices.'],
            ]],
            ['question' => 'Strong cyber hygiene ultimately helps to:', 'answers' => [
                ['text' => 'Eliminate all threats', 'correct' => false, 'explanation' => 'Incorrect: Cannot eliminate all threats.'],
                ['text' => 'Reduce personal and organizational cyber risk', 'correct' => true, 'explanation' => 'Correct: Prevention lowers overall risk exposure.'],
                ['text' => 'Replace security teams', 'correct' => false, 'explanation' => 'Incorrect: Complements teams.'],
                ['text' => 'Avoid technology', 'correct' => false, 'explanation' => 'Incorrect: Enables safe technology use.'],
            ]],
        ];
    }

    private function getLesson11Questions(): array
    {
        return [
            ['question' => 'Organizational security focuses on protecting:', 'answers' => [
                ['text' => 'Only computers', 'correct' => false, 'explanation' => 'Incorrect: Broader scope.'],
                ['text' => 'Systems, data, and people within an organization', 'correct' => true, 'explanation' => 'Correct: Workplace security includes people, data, and systems.'],
                ['text' => 'Home networks', 'correct' => false, 'explanation' => 'Incorrect: Different context.'],
                ['text' => 'Personal devices only', 'correct' => false, 'explanation' => 'Incorrect: Organizational scope.'],
            ]],
            ['question' => 'Why are employees critical to workplace security?', 'answers' => [
                ['text' => 'They manage servers', 'correct' => false, 'explanation' => 'Incorrect: IT role.'],
                ['text' => 'Their actions can prevent or cause incidents', 'correct' => true, 'explanation' => 'Correct: Human behavior strongly impacts security.'],
                ['text' => 'They control laws', 'correct' => false, 'explanation' => 'Incorrect: Government role.'],
                ['text' => 'They replace IT staff', 'correct' => false, 'explanation' => 'Incorrect: Different roles.'],
            ]],
            ['question' => 'Security policies are created to:', 'answers' => [
                ['text' => 'Restrict productivity', 'correct' => false, 'explanation' => 'Incorrect: Enable safe productivity.'],
                ['text' => 'Define acceptable and unacceptable behavior', 'correct' => true, 'explanation' => 'Correct: Policies guide safe workplace behavior.'],
                ['text' => 'Replace laws', 'correct' => false, 'explanation' => 'Incorrect: Complement laws.'],
                ['text' => 'Control personal devices', 'correct' => false, 'explanation' => 'Incorrect: Focus on organizational resources.'],
            ]],
            ['question' => 'Acceptable use policies usually cover:', 'answers' => [
                ['text' => 'Personal hobbies', 'correct' => false, 'explanation' => 'Incorrect: Work-related.'],
                ['text' => 'Device and system usage rules', 'correct' => true, 'explanation' => 'Correct: AUPs define proper use of resources.'],
                ['text' => 'Salary information', 'correct' => false, 'explanation' => 'Incorrect: HR domain.'],
                ['text' => 'Office design', 'correct' => false, 'explanation' => 'Incorrect: Facilities domain.'],
            ]],
            ['question' => 'The principle of least privilege means:', 'answers' => [
                ['text' => 'Everyone has full access', 'correct' => false, 'explanation' => 'Incorrect: Opposite approach.'],
                ['text' => 'Access is limited to what is necessary for the role', 'correct' => true, 'explanation' => 'Correct: Limiting access reduces damage from compromise.'],
                ['text' => 'Access is temporary', 'correct' => false, 'explanation' => 'Incorrect: About scope, not duration.'],
                ['text' => 'Privileges are shared', 'correct' => false, 'explanation' => 'Incorrect: Opposite approach.'],
            ]],
            ['question' => 'Why is least privilege important?', 'answers' => [
                ['text' => 'It slows systems', 'correct' => false, 'explanation' => 'Incorrect: Security benefit.'],
                ['text' => 'It limits damage if an account is compromised', 'correct' => true, 'explanation' => 'Correct: Less access means less impact.'],
                ['text' => 'It reduces updates', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
                ['text' => 'It replaces MFA', 'correct' => false, 'explanation' => 'Incorrect: Complements MFA.'],
            ]],
            ['question' => 'Which is an example of physical security?', 'answers' => [
                ['text' => 'Antivirus software', 'correct' => false, 'explanation' => 'Incorrect: Technical control.'],
                ['text' => 'ID badges and locked doors', 'correct' => true, 'explanation' => 'Correct: Physical controls protect facilities.'],
                ['text' => 'Encryption', 'correct' => false, 'explanation' => 'Incorrect: Technical control.'],
                ['text' => 'Firewalls', 'correct' => false, 'explanation' => 'Incorrect: Technical control.'],
            ]],
            ['question' => 'Visitors should be required to:', 'answers' => [
                ['text' => 'Walk freely', 'correct' => false, 'explanation' => 'Incorrect: Security risk.'],
                ['text' => 'Sign in and be escorted', 'correct' => true, 'explanation' => 'Correct: Visitor controls prevent unauthorized access.'],
                ['text' => 'Use employee credentials', 'correct' => false, 'explanation' => 'Incorrect: Security violation.'],
                ['text' => 'Access systems', 'correct' => false, 'explanation' => 'Incorrect: Unauthorized.'],
            ]],
            ['question' => 'Why should screens be locked when unattended?', 'answers' => [
                ['text' => 'To save power', 'correct' => false, 'explanation' => 'Incorrect: Not the security purpose.'],
                ['text' => 'To prevent unauthorized access to information', 'correct' => true, 'explanation' => 'Correct: Screen locks protect data.'],
                ['text' => 'To improve speed', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
                ['text' => 'To hide updates', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Workplace email is a target because it:', 'answers' => [
                ['text' => 'Is slow', 'correct' => false, 'explanation' => 'Incorrect: Not the reason.'],
                ['text' => 'Can be used for phishing and impersonation', 'correct' => true, 'explanation' => 'Correct: Email is a common attack vector.'],
                ['text' => 'Is encrypted', 'correct' => false, 'explanation' => 'Incorrect: Encryption doesn\'t prevent targeting.'],
                ['text' => 'Has no users', 'correct' => false, 'explanation' => 'Incorrect: Widely used.'],
            ]],
            ['question' => 'Unexpected requests from management should be:', 'answers' => [
                ['text' => 'Obeyed immediately', 'correct' => false, 'explanation' => 'Incorrect: Should verify.'],
                ['text' => 'Verified through proper channels', 'correct' => true, 'explanation' => 'Correct: Verification prevents impersonation scams.'],
                ['text' => 'Ignored', 'correct' => false, 'explanation' => 'Incorrect: Should verify, not ignore.'],
                ['text' => 'Shared publicly', 'correct' => false, 'explanation' => 'Incorrect: May expose sensitive info.'],
            ]],
            ['question' => 'What should employees do if they suspect a security incident?', 'answers' => [
                ['text' => 'Ignore it', 'correct' => false, 'explanation' => 'Incorrect: Should report.'],
                ['text' => 'Report it immediately according to procedure', 'correct' => true, 'explanation' => 'Correct: Early reporting reduces damage.'],
                ['text' => 'Fix it alone', 'correct' => false, 'explanation' => 'Incorrect: Should follow procedure.'],
                ['text' => 'Delete evidence', 'correct' => false, 'explanation' => 'Incorrect: Should preserve evidence.'],
            ]],
            ['question' => 'Incident response aims to:', 'answers' => [
                ['text' => 'Assign blame', 'correct' => false, 'explanation' => 'Incorrect: Focus on resolution.'],
                ['text' => 'Contain and resolve security issues', 'correct' => true, 'explanation' => 'Correct: Response minimizes impact.'],
                ['text' => 'Shut down all systems', 'correct' => false, 'explanation' => 'Incorrect: Targeted response.'],
                ['text' => 'Delay operations', 'correct' => false, 'explanation' => 'Incorrect: Aims to restore quickly.'],
            ]],
            ['question' => 'Sharing passwords with coworkers is:', 'answers' => [
                ['text' => 'Efficient', 'correct' => false, 'explanation' => 'Incorrect: Security violation.'],
                ['text' => 'Acceptable', 'correct' => false, 'explanation' => 'Incorrect: Violates policy.'],
                ['text' => 'A violation of security policy', 'correct' => true, 'explanation' => 'Correct: Credentials must remain private.'],
                ['text' => 'Required', 'correct' => false, 'explanation' => 'Incorrect: Never required.'],
            ]],
            ['question' => 'Which behavior strengthens workplace security?', 'answers' => [
                ['text' => 'Password sharing', 'correct' => false, 'explanation' => 'Incorrect: Weakens security.'],
                ['text' => 'Policy compliance and awareness', 'correct' => true, 'explanation' => 'Correct: Compliance supports defense.'],
                ['text' => 'Ignoring training', 'correct' => false, 'explanation' => 'Incorrect: Training is important.'],
                ['text' => 'Using personal devices freely', 'correct' => false, 'explanation' => 'Incorrect: May violate policy.'],
            ]],
            ['question' => 'Clear desk policies help because they:', 'answers' => [
                ['text' => 'Improve cleanliness', 'correct' => false, 'explanation' => 'Incorrect: Security purpose.'],
                ['text' => 'Reduce exposure of sensitive information', 'correct' => true, 'explanation' => 'Correct: Visible documents can leak data.'],
                ['text' => 'Save time', 'correct' => false, 'explanation' => 'Incorrect: Not the primary purpose.'],
                ['text' => 'Increase storage', 'correct' => false, 'explanation' => 'Incorrect: Not the purpose.'],
            ]],
            ['question' => 'Why is security a shared responsibility?', 'answers' => [
                ['text' => 'IT cannot help', 'correct' => false, 'explanation' => 'Incorrect: IT helps but everyone plays a role.'],
                ['text' => 'Everyone\'s actions affect overall security', 'correct' => true, 'explanation' => 'Correct: Human behavior matters.'],
                ['text' => 'Policies are optional', 'correct' => false, 'explanation' => 'Incorrect: Policies are mandatory.'],
                ['text' => 'Systems are automatic', 'correct' => false, 'explanation' => 'Incorrect: Requires human participation.'],
            ]],
            ['question' => 'Which action may introduce insider risk?', 'answers' => [
                ['text' => 'Following procedures', 'correct' => false, 'explanation' => 'Incorrect: Reduces risk.'],
                ['text' => 'Excessive access privileges', 'correct' => true, 'explanation' => 'Correct: Too much access increases risk.'],
                ['text' => 'MFA usage', 'correct' => false, 'explanation' => 'Incorrect: Reduces risk.'],
                ['text' => 'Reporting incidents', 'correct' => false, 'explanation' => 'Incorrect: Reduces risk.'],
            ]],
            ['question' => 'Training employees improves security by:', 'answers' => [
                ['text' => 'Slowing work', 'correct' => false, 'explanation' => 'Incorrect: Improves efficiency.'],
                ['text' => 'Increasing awareness of threats and policies', 'correct' => true, 'explanation' => 'Correct: Awareness reduces mistakes.'],
                ['text' => 'Replacing controls', 'correct' => false, 'explanation' => 'Incorrect: Complements controls.'],
                ['text' => 'Eliminating risk', 'correct' => false, 'explanation' => 'Incorrect: Reduces but doesn\'t eliminate.'],
            ]],
            ['question' => 'Effective workplace security ultimately aims to:', 'answers' => [
                ['text' => 'Restrict employees', 'correct' => false, 'explanation' => 'Incorrect: Enable safe work.'],
                ['text' => 'Protect organizational operations and data', 'correct' => true, 'explanation' => 'Correct: Security supports safe operations.'],
                ['text' => 'Eliminate technology', 'correct' => false, 'explanation' => 'Incorrect: Enable safe technology use.'],
                ['text' => 'Avoid reporting', 'correct' => false, 'explanation' => 'Incorrect: Encourage reporting.'],
            ]],
        ];
    }

    private function getLesson12Questions(): array
    {
        return [
            ['question' => 'What defines an emerging cyber threat?', 'answers' => [
                ['text' => 'An outdated attack', 'correct' => false, 'explanation' => 'Incorrect: Emerging means new.'],
                ['text' => 'A threat that uses new technologies or methods', 'correct' => true, 'explanation' => 'Correct: Emerging threats exploit new tools or behaviors.'],
                ['text' => 'A harmless activity', 'correct' => false, 'explanation' => 'Incorrect: Threats are harmful.'],
                ['text' => 'A system update', 'correct' => false, 'explanation' => 'Incorrect: Updates improve security.'],
            ]],
            ['question' => 'Why do cyber threats continue to evolve?', 'answers' => [
                ['text' => 'Technology never changes', 'correct' => false, 'explanation' => 'Incorrect: Technology constantly evolves.'],
                ['text' => 'Attackers adapt to new defenses and technologies', 'correct' => true, 'explanation' => 'Correct: Attackers constantly adapt.'],
                ['text' => 'Laws stop attacks', 'correct' => false, 'explanation' => 'Incorrect: Laws deter but don\'t stop all attacks.'],
                ['text' => 'Security eliminates risk', 'correct' => false, 'explanation' => 'Incorrect: Risk can\'t be eliminated.'],
            ]],
            ['question' => 'AI-driven attacks are dangerous because they:', 'answers' => [
                ['text' => 'Are slower', 'correct' => false, 'explanation' => 'Incorrect: AI speeds up attacks.'],
                ['text' => 'Can scale and personalize attacks efficiently', 'correct' => true, 'explanation' => 'Correct: Automation increases reach and effectiveness.'],
                ['text' => 'Require manual effort', 'correct' => false, 'explanation' => 'Incorrect: AI reduces manual work.'],
                ['text' => 'Reduce targeting', 'correct' => false, 'explanation' => 'Incorrect: AI improves targeting.'],
            ]],
            ['question' => 'Automation helps attackers by:', 'answers' => [
                ['text' => 'Increasing cost', 'correct' => false, 'explanation' => 'Incorrect: Reduces cost.'],
                ['text' => 'Allowing large-scale attacks with little effort', 'correct' => true, 'explanation' => 'Correct: Automation enables mass targeting.'],
                ['text' => 'Reducing realism', 'correct' => false, 'explanation' => 'Incorrect: Automation can improve realism.'],
                ['text' => 'Limiting reach', 'correct' => false, 'explanation' => 'Incorrect: Expands reach.'],
            ]],
            ['question' => 'An expanding attack surface refers to:', 'answers' => [
                ['text' => 'Fewer devices', 'correct' => false, 'explanation' => 'Incorrect: More devices expand surface.'],
                ['text' => 'More connected systems and entry points', 'correct' => true, 'explanation' => 'Correct: More connectivity increases exposure.'],
                ['text' => 'Smaller networks', 'correct' => false, 'explanation' => 'Incorrect: Networks are growing.'],
                ['text' => 'Offline environments', 'correct' => false, 'explanation' => 'Incorrect: About connected systems.'],
            ]],
            ['question' => 'Which technology increases attack surface the most?', 'answers' => [
                ['text' => 'Offline storage', 'correct' => false, 'explanation' => 'Incorrect: Not connected.'],
                ['text' => 'Internet-connected devices (IoT)', 'correct' => true, 'explanation' => 'Correct: IoT devices often lack strong security.'],
                ['text' => 'Paper records', 'correct' => false, 'explanation' => 'Incorrect: Physical, not digital.'],
                ['text' => 'Manual processes', 'correct' => false, 'explanation' => 'Incorrect: Not digital.'],
            ]],
            ['question' => 'Supply chain attacks are dangerous because they:', 'answers' => [
                ['text' => 'Affect only one system', 'correct' => false, 'explanation' => 'Incorrect: Affect many.'],
                ['text' => 'Can impact many organizations through one vendor', 'correct' => true, 'explanation' => 'Correct: One breach can spread widely.'],
                ['text' => 'Are easy to detect', 'correct' => false, 'explanation' => 'Incorrect: Difficult to detect.'],
                ['text' => 'Are always accidental', 'correct' => false, 'explanation' => 'Incorrect: Often intentional.'],
            ]],
            ['question' => 'Third-party risk refers to threats from:', 'answers' => [
                ['text' => 'Internal users', 'correct' => false, 'explanation' => 'Incorrect: Third-party means external.'],
                ['text' => 'External service providers and vendors', 'correct' => true, 'explanation' => 'Correct: Vendors can introduce vulnerabilities.'],
                ['text' => 'Personal devices only', 'correct' => false, 'explanation' => 'Incorrect: Broader than devices.'],
                ['text' => 'Offline systems', 'correct' => false, 'explanation' => 'Incorrect: About connected services.'],
            ]],
            ['question' => 'Modern attackers increasingly target:', 'answers' => [
                ['text' => 'Hardware only', 'correct' => false, 'explanation' => 'Incorrect: Data is primary target.'],
                ['text' => 'Data and personal information', 'correct' => true, 'explanation' => 'Correct: Data is valuable for fraud and identity theft.'],
                ['text' => 'Office furniture', 'correct' => false, 'explanation' => 'Incorrect: Not a cyber target.'],
                ['text' => 'Internet cables', 'correct' => false, 'explanation' => 'Incorrect: Not typical target.'],
            ]],
            ['question' => 'Why is stolen data reused over time?', 'answers' => [
                ['text' => 'It expires quickly', 'correct' => false, 'explanation' => 'Incorrect: Remains valuable.'],
                ['text' => 'It enables future scams and impersonation', 'correct' => true, 'explanation' => 'Correct: Reused data increases long-term risk.'],
                ['text' => 'It improves privacy', 'correct' => false, 'explanation' => 'Incorrect: Opposite effect.'],
                ['text' => 'It loses value', 'correct' => false, 'explanation' => 'Incorrect: Retains value.'],
            ]],
            ['question' => 'Privacy risks increase because:', 'answers' => [
                ['text' => 'Less data is stored', 'correct' => false, 'explanation' => 'Incorrect: More data is stored.'],
                ['text' => 'More personal data is collected digitally', 'correct' => true, 'explanation' => 'Correct: More data increases exposure.'],
                ['text' => 'Systems are offline', 'correct' => false, 'explanation' => 'Incorrect: Systems are increasingly online.'],
                ['text' => 'Encryption is used', 'correct' => false, 'explanation' => 'Incorrect: Encryption protects privacy.'],
            ]],
            ['question' => 'Which trend increases risk in remote work environments?', 'answers' => [
                ['text' => 'Centralized offices', 'correct' => false, 'explanation' => 'Incorrect: Remote is decentralized.'],
                ['text' => 'Home networks and personal devices', 'correct' => true, 'explanation' => 'Correct: Home setups are often less secure.'],
                ['text' => 'Locked server rooms', 'correct' => false, 'explanation' => 'Incorrect: Security measure.'],
                ['text' => 'Physical security', 'correct' => false, 'explanation' => 'Incorrect: Less relevant remotely.'],
            ]],
            ['question' => 'Preparing for future threats requires:', 'answers' => [
                ['text' => 'One-time training', 'correct' => false, 'explanation' => 'Incorrect: Ongoing needed.'],
                ['text' => 'Continuous learning and adaptation', 'correct' => true, 'explanation' => 'Correct: Threats evolve constantly.'],
                ['text' => 'Ignoring trends', 'correct' => false, 'explanation' => 'Incorrect: Must follow trends.'],
                ['text' => 'Manual monitoring only', 'correct' => false, 'explanation' => 'Incorrect: Use automated tools.'],
            ]],
            ['question' => 'Defense-in-depth means:', 'answers' => [
                ['text' => 'One security tool', 'correct' => false, 'explanation' => 'Incorrect: Multiple layers.'],
                ['text' => 'Multiple layers of security controls', 'correct' => true, 'explanation' => 'Correct: Layers reduce single points of failure.'],
                ['text' => 'No monitoring', 'correct' => false, 'explanation' => 'Incorrect: Monitoring is essential.'],
                ['text' => 'Faster responses', 'correct' => false, 'explanation' => 'Incorrect: About layers, not speed.'],
            ]],
            ['question' => 'Monitoring threat intelligence helps by:', 'answers' => [
                ['text' => 'Replacing security teams', 'correct' => false, 'explanation' => 'Incorrect: Supports teams.'],
                ['text' => 'Identifying emerging risks early', 'correct' => true, 'explanation' => 'Correct: Awareness supports proactive defense.'],
                ['text' => 'Eliminating attacks', 'correct' => false, 'explanation' => 'Incorrect: Can\'t eliminate all attacks.'],
                ['text' => 'Disabling systems', 'correct' => false, 'explanation' => 'Incorrect: Unrelated.'],
            ]],
            ['question' => 'Which habit supports resilience against future threats?', 'answers' => [
                ['text' => 'Ignoring alerts', 'correct' => false, 'explanation' => 'Incorrect: Pay attention to alerts.'],
                ['text' => 'Prompt incident reporting', 'correct' => true, 'explanation' => 'Correct: Reporting limits damage and spread.'],
                ['text' => 'Password reuse', 'correct' => false, 'explanation' => 'Incorrect: Weakens security.'],
                ['text' => 'Avoiding updates', 'correct' => false, 'explanation' => 'Incorrect: Updates are important.'],
            ]],
            ['question' => 'Emerging threats are harder to stop because they:', 'answers' => [
                ['text' => 'Use familiar patterns', 'correct' => false, 'explanation' => 'Incorrect: Use new patterns.'],
                ['text' => 'Bypass outdated defenses', 'correct' => true, 'explanation' => 'Correct: New methods evade old controls.'],
                ['text' => 'Are well documented', 'correct' => false, 'explanation' => 'Incorrect: New means less documented.'],
                ['text' => 'Are rare', 'correct' => false, 'explanation' => 'Incorrect: Becoming more common.'],
            ]],
            ['question' => 'Which factor increases long-term cyber risk?', 'answers' => [
                ['text' => 'Strong hygiene', 'correct' => false, 'explanation' => 'Incorrect: Reduces risk.'],
                ['text' => 'Poor adaptation to new threats', 'correct' => true, 'explanation' => 'Correct: Failure to adapt increases exposure.'],
                ['text' => 'Regular updates', 'correct' => false, 'explanation' => 'Incorrect: Reduces risk.'],
                ['text' => 'Awareness training', 'correct' => false, 'explanation' => 'Incorrect: Reduces risk.'],
            ]],
            ['question' => 'Future cybersecurity strategies should focus on:', 'answers' => [
                ['text' => 'Static defenses', 'correct' => false, 'explanation' => 'Incorrect: Need flexibility.'],
                ['text' => 'Flexibility and preparedness', 'correct' => true, 'explanation' => 'Correct: Flexibility enables response to change.'],
                ['text' => 'Eliminating internet use', 'correct' => false, 'explanation' => 'Incorrect: Not practical.'],
                ['text' => 'Manual controls only', 'correct' => false, 'explanation' => 'Incorrect: Need automation.'],
            ]],
            ['question' => 'Understanding emerging threats helps organizations to:', 'answers' => [
                ['text' => 'Delay response', 'correct' => false, 'explanation' => 'Incorrect: Enables faster response.'],
                ['text' => 'Prepare proactively and reduce impact', 'correct' => true, 'explanation' => 'Correct: Proactive preparation reduces damage.'],
                ['text' => 'Eliminate all risks', 'correct' => false, 'explanation' => 'Incorrect: Can\'t eliminate all risks.'],
                ['text' => 'Avoid reporting', 'correct' => false, 'explanation' => 'Incorrect: Should report.'],
            ]],
        ];
    }

    private function getLesson13Questions(): array
    {
        return [
            ['question' => 'Strategic cybersecurity planning focuses on:', 'answers' => [
                ['text' => 'Daily password changes', 'correct' => false, 'explanation' => 'Incorrect: Operational, not strategic.'],
                ['text' => 'Long-term protection and risk management', 'correct' => true, 'explanation' => 'Correct: Strategic planning addresses long-term risks.'],
                ['text' => 'Software installation only', 'correct' => false, 'explanation' => 'Incorrect: Too narrow.'],
                ['text' => 'Eliminating all threats', 'correct' => false, 'explanation' => 'Incorrect: Impossible goal.'],
            ]],
            ['question' => 'National cybersecurity readiness aims to protect:', 'answers' => [
                ['text' => 'Only private companies', 'correct' => false, 'explanation' => 'Incorrect: Broader scope.'],
                ['text' => 'Critical systems and national interests', 'correct' => true, 'explanation' => 'Correct: Readiness protects essential services and stability.'],
                ['text' => 'Social media accounts', 'correct' => false, 'explanation' => 'Incorrect: Too narrow.'],
                ['text' => 'Personal devices only', 'correct' => false, 'explanation' => 'Incorrect: National scope.'],
            ]],
            ['question' => 'Which asset is considered critical infrastructure?', 'answers' => [
                ['text' => 'Personal blogs', 'correct' => false, 'explanation' => 'Incorrect: Not critical.'],
                ['text' => 'Energy and healthcare systems', 'correct' => true, 'explanation' => 'Correct: Critical infrastructure supports essential services.'],
                ['text' => 'Entertainment apps', 'correct' => false, 'explanation' => 'Incorrect: Not critical.'],
                ['text' => 'Online games', 'correct' => false, 'explanation' => 'Incorrect: Not critical.'],
            ]],
            ['question' => 'Risk assessment helps organizations to:', 'answers' => [
                ['text' => 'Ignore threats', 'correct' => false, 'explanation' => 'Incorrect: Addresses threats.'],
                ['text' => 'Identify and prioritize risks', 'correct' => true, 'explanation' => 'Correct: Risk assessment guides planning.'],
                ['text' => 'Delay action', 'correct' => false, 'explanation' => 'Incorrect: Enables action.'],
                ['text' => 'Remove all systems', 'correct' => false, 'explanation' => 'Incorrect: Protects systems.'],
            ]],
            ['question' => 'Strategic cybersecurity planning should align with:', 'answers' => [
                ['text' => 'Personal preferences', 'correct' => false, 'explanation' => 'Incorrect: Should align with organizational goals.'],
                ['text' => 'Organizational or national objectives', 'correct' => true, 'explanation' => 'Correct: Alignment ensures security supports goals.'],
                ['text' => 'Random decisions', 'correct' => false, 'explanation' => 'Incorrect: Should be systematic.'],
                ['text' => 'Short-term fixes', 'correct' => false, 'explanation' => 'Incorrect: Strategic is long-term.'],
            ]],
            ['question' => 'National readiness requires coordination among:', 'answers' => [
                ['text' => 'One agency', 'correct' => false, 'explanation' => 'Incorrect: Requires multiple stakeholders.'],
                ['text' => 'Multiple stakeholders', 'correct' => true, 'explanation' => 'Correct: Cybersecurity is a shared responsibility.'],
                ['text' => 'Only IT teams', 'correct' => false, 'explanation' => 'Incorrect: Broader participation needed.'],
                ['text' => 'Foreign governments only', 'correct' => false, 'explanation' => 'Incorrect: Domestic coordination is primary.'],
            ]],
            ['question' => 'CERT/CSIRT teams primarily handle:', 'answers' => [
                ['text' => 'Marketing', 'correct' => false, 'explanation' => 'Incorrect: Different function.'],
                ['text' => 'Incident response and coordination', 'correct' => true, 'explanation' => 'Correct: CERT/CSIRT teams manage cyber incidents.'],
                ['text' => 'Budget planning', 'correct' => false, 'explanation' => 'Incorrect: Different function.'],
                ['text' => 'Software development', 'correct' => false, 'explanation' => 'Incorrect: Different function.'],
            ]],
            ['question' => 'Preparedness activities include:', 'answers' => [
                ['text' => 'Ignoring drills', 'correct' => false, 'explanation' => 'Incorrect: Drills are important.'],
                ['text' => 'Incident response planning and exercises', 'correct' => true, 'explanation' => 'Correct: Exercises improve response capability.'],
                ['text' => 'System shutdowns', 'correct' => false, 'explanation' => 'Incorrect: Not preparedness.'],
                ['text' => 'Reducing staff', 'correct' => false, 'explanation' => 'Incorrect: Need adequate staffing.'],
            ]],
            ['question' => 'Cyber resilience focuses on:', 'answers' => [
                ['text' => 'Preventing all attacks', 'correct' => false, 'explanation' => 'Incorrect: Impossible goal.'],
                ['text' => 'Maintaining services and recovering quickly', 'correct' => true, 'explanation' => 'Correct: Resilience limits impact and downtime.'],
                ['text' => 'Eliminating technology', 'correct' => false, 'explanation' => 'Incorrect: Opposite approach.'],
                ['text' => 'Avoiding cooperation', 'correct' => false, 'explanation' => 'Incorrect: Cooperation is essential.'],
            ]],
            ['question' => 'Why is international cooperation important?', 'answers' => [
                ['text' => 'Cybercrime is local only', 'correct' => false, 'explanation' => 'Incorrect: Cybercrime is global.'],
                ['text' => 'Cyber threats cross borders', 'correct' => true, 'explanation' => 'Correct: Global coordination improves defense.'],
                ['text' => 'It delays response', 'correct' => false, 'explanation' => 'Incorrect: Speeds response.'],
                ['text' => 'It replaces national laws', 'correct' => false, 'explanation' => 'Incorrect: Complements national laws.'],
            ]],
            ['question' => 'Threat intelligence sharing helps by:', 'answers' => [
                ['text' => 'Hiding incidents', 'correct' => false, 'explanation' => 'Incorrect: Opposite effect.'],
                ['text' => 'Improving early detection and response', 'correct' => true, 'explanation' => 'Correct: Shared intelligence strengthens preparedness.'],
                ['text' => 'Increasing confusion', 'correct' => false, 'explanation' => 'Incorrect: Improves clarity.'],
                ['text' => 'Reducing transparency', 'correct' => false, 'explanation' => 'Incorrect: Increases transparency.'],
            ]],
            ['question' => 'Public awareness programs help to:', 'answers' => [
                ['text' => 'Replace technology', 'correct' => false, 'explanation' => 'Incorrect: Complement technology.'],
                ['text' => 'Reduce human-related cyber risks', 'correct' => true, 'explanation' => 'Correct: Informed users make safer choices.'],
                ['text' => 'Increase attacks', 'correct' => false, 'explanation' => 'Incorrect: Opposite effect.'],
                ['text' => 'Avoid education', 'correct' => false, 'explanation' => 'Incorrect: Promote education.'],
            ]],
            ['question' => 'Workforce development supports national readiness by:', 'answers' => [
                ['text' => 'Reducing skills', 'correct' => false, 'explanation' => 'Incorrect: Builds skills.'],
                ['text' => 'Building cybersecurity expertise', 'correct' => true, 'explanation' => 'Correct: Skilled professionals strengthen capacity.'],
                ['text' => 'Eliminating jobs', 'correct' => false, 'explanation' => 'Incorrect: Creates jobs.'],
                ['text' => 'Outsourcing all roles', 'correct' => false, 'explanation' => 'Incorrect: Build domestic capacity.'],
            ]],
            ['question' => 'National cybersecurity strategies should be:', 'answers' => [
                ['text' => 'Static and unchanging', 'correct' => false, 'explanation' => 'Incorrect: Should evolve.'],
                ['text' => 'Regularly reviewed and updated', 'correct' => true, 'explanation' => 'Correct: Threats evolve, strategies must adapt.'],
                ['text' => 'Informal', 'correct' => false, 'explanation' => 'Incorrect: Should be formal.'],
                ['text' => 'Technology-only', 'correct' => false, 'explanation' => 'Incorrect: Include people and process.'],
            ]],
            ['question' => 'Which action improves national resilience?', 'answers' => [
                ['text' => 'Ignoring incidents', 'correct' => false, 'explanation' => 'Incorrect: Should respond.'],
                ['text' => 'Coordinated response planning', 'correct' => true, 'explanation' => 'Correct: Coordination reduces impact.'],
                ['text' => 'Isolated efforts', 'correct' => false, 'explanation' => 'Incorrect: Coordination is needed.'],
                ['text' => 'Delayed reporting', 'correct' => false, 'explanation' => 'Incorrect: Prompt reporting needed.'],
            ]],
            ['question' => 'Private sector organizations are important because they:', 'answers' => [
                ['text' => 'Have no role', 'correct' => false, 'explanation' => 'Incorrect: Critical role.'],
                ['text' => 'Operate much of the critical infrastructure', 'correct' => true, 'explanation' => 'Correct: Many essential services are privately operated.'],
                ['text' => 'Avoid regulation', 'correct' => false, 'explanation' => 'Incorrect: Subject to regulation.'],
                ['text' => 'Only handle profits', 'correct' => false, 'explanation' => 'Incorrect: Have security responsibilities.'],
            ]],
            ['question' => 'Educational institutions contribute by:', 'answers' => [
                ['text' => 'Ignoring security', 'correct' => false, 'explanation' => 'Incorrect: Promote security.'],
                ['text' => 'Training future cybersecurity professionals', 'correct' => true, 'explanation' => 'Correct: Education builds long-term capacity.'],
                ['text' => 'Avoiding technology', 'correct' => false, 'explanation' => 'Incorrect: Embrace technology safely.'],
                ['text' => 'Reducing awareness', 'correct' => false, 'explanation' => 'Incorrect: Increase awareness.'],
            ]],
            ['question' => 'Which factor strengthens national cybersecurity posture?', 'answers' => [
                ['text' => 'Lack of policy', 'correct' => false, 'explanation' => 'Incorrect: Need clear policies.'],
                ['text' => 'Clear laws and enforcement mechanisms', 'correct' => true, 'explanation' => 'Correct: Legal frameworks support enforcement.'],
                ['text' => 'Ignoring threats', 'correct' => false, 'explanation' => 'Incorrect: Must address threats.'],
                ['text' => 'Minimal coordination', 'correct' => false, 'explanation' => 'Incorrect: Need strong coordination.'],
            ]],
            ['question' => 'Strategic cybersecurity planning is most effective when it is:', 'answers' => [
                ['text' => 'Reactive only', 'correct' => false, 'explanation' => 'Incorrect: Should be proactive.'],
                ['text' => 'Proactive and risk-based', 'correct' => true, 'explanation' => 'Correct: Proactive planning reduces impact.'],
                ['text' => 'Temporary', 'correct' => false, 'explanation' => 'Incorrect: Should be ongoing.'],
                ['text' => 'Informal', 'correct' => false, 'explanation' => 'Incorrect: Should be structured.'],
            ]],
            ['question' => 'The ultimate goal of national cybersecurity readiness is to:', 'answers' => [
                ['text' => 'Eliminate all cyber incidents', 'correct' => false, 'explanation' => 'Incorrect: Impossible goal.'],
                ['text' => 'Protect society, economy, and national security', 'correct' => true, 'explanation' => 'Correct: Readiness supports national stability and safety.'],
                ['text' => 'Stop internet use', 'correct' => false, 'explanation' => 'Incorrect: Enable safe internet use.'],
                ['text' => 'Avoid cooperation', 'correct' => false, 'explanation' => 'Incorrect: Promote cooperation.'],
            ]],
        ];
    }

    private function createStudents(int $count): array
    {
        $students = [];
        $firstNames = ['James', 'Mary', 'John', 'Patricia', 'Robert', 'Jennifer', 'Michael', 'Linda', 
                       'William', 'Elizabeth', 'David', 'Barbara', 'Richard', 'Susan', 'Joseph', 'Jessica',
                       'Thomas', 'Sarah', 'Charles', 'Karen', 'Christopher', 'Nancy', 'Daniel', 'Lisa',
                       'Matthew', 'Betty', 'Anthony', 'Margaret', 'Mark', 'Sandra', 'Donald', 'Ashley',
                       'Steven', 'Kimberly', 'Paul', 'Emily', 'Andrew', 'Donna', 'Joshua', 'Michelle',
                       'Kenneth', 'Carol', 'Kevin', 'Amanda', 'Brian', 'Dorothy', 'George', 'Melissa',
                       'Timothy', 'Deborah', 'Ronald', 'Stephanie', 'Edward', 'Rebecca', 'Jason', 'Sharon'];
        
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis',
                      'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas',
                      'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson', 'White',
                      'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Walker', 'Young',
                      'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill', 'Flores',
                      'Green', 'Adams', 'Nelson', 'Baker', 'Hall', 'Rivera', 'Campbell', 'Mitchell'];

        for ($i = 0; $i < $count; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            
            $student = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => strtolower($firstName . '.' . $lastName . ($i + 1) . '@example.com'),
                'user_type' => 'USER',
                'password' => Hash::make('password'),
                'status' => true,
                'created_at' => now()->subDays(rand(1, 90))
            ]);
            
            $students[] = $student;
        }

        return $students;
    }

    /**
     * Create realistic student progress
     */
    private function createStudentProgress($student, $lessons): void
    {
        // Determine student performance level (affects completion rate and scores)
        $performanceLevel = rand(1, 10); // 1-3: struggling, 4-7: average, 8-10: excellent
        
        // Determine how far the student has progressed
        $progressPercentage = $this->getProgressPercentage($performanceLevel);
        $lessonsToAttempt = (int) ceil($lessons->count() * $progressPercentage);
        
        $completedLessons = 0;
        $currentDay = rand(1, 60); // Starting day for this student's progress
        
        foreach ($lessons as $index => $lesson) {
            if ($index >= $lessonsToAttempt) {
                break;
            }

            // Check if prerequisite is completed (for sequential lessons)
            if ($lesson->prerequisite_lesson_id) {
                $prerequisiteProgress = StudentLesson::where('user_id', $student->id)
                    ->where('lesson_id', $lesson->prerequisite_lesson_id)
                    ->first();
                
                // If prerequisite not completed, stop here
                if (!$prerequisiteProgress || !$prerequisiteProgress->completed_at) {
                    break;
                }
            }

            // First lesson is always unlocked, others unlock after completing prerequisite
            $isUnlocked = $lesson->prerequisite_lesson_id === null || $completedLessons > 0;

            // Create student lesson record
            $studentLesson = StudentLesson::create([
                'user_id' => $student->id,
                'lesson_id' => $lesson->id,
                'is_unlocked' => $isUnlocked,
                'content_viewed' => true,
                'quiz_passed' => false,
                'simulations_completed' => false,
                'simulation_progress' => 0,
                'best_score' => null,
                'completed_at' => null,
                'created_at' => now()->subDays($currentDay),
            ]);

            $lessonCompleted = false;

            // Handle quiz attempts
            if ($lesson->quiz && $lesson->quiz->is_active) {
                $quizPassed = $this->createQuizAttempts($student, $lesson, $studentLesson, $performanceLevel, $currentDay);
                $studentLesson->refresh();
            } else {
                $quizPassed = true; // No quiz means quiz requirement is met
            }

            // Handle simulation attempts (only for lessons 1, 2, 3)
            if ($lesson->has_simulation && $lesson->id <= 3) {
                $simPassed = $this->createSimulationAttempts($student, $lesson, $studentLesson, $performanceLevel, $currentDay);
                $studentLesson->refresh();
            } else {
                $simPassed = true; // No simulation means simulation requirement is met
            }

            // Update completion status if both requirements are met
            if ($quizPassed && $simPassed) {
                $studentLesson->completed_at = now()->subDays($currentDay - rand(1, 3));
                $studentLesson->save();
                $completedLessons++;
                $lessonCompleted = true;
                
                // Move to next lesson (add some days between lessons)
                $currentDay = max(1, $currentDay - rand(2, 7));
            } else {
                // Student failed and stopped progressing
                break;
            }
        }
    }

    /**
     * Create quiz attempts for a student
     */
    private function createQuizAttempts($student, $lesson, $studentLesson, $performanceLevel, $currentDay): bool
    {
        $quiz = $lesson->quiz;
        $questions = Question::where('quiz_id', $quiz->id)->with('answers')->get();
        
        if ($questions->isEmpty()) {
            return true;
        }

        // Determine number of attempts based on performance and difficulty
        $difficultyMultiplier = $this->getDifficultyMultiplier($lesson->difficulty);
        
        if ($performanceLevel <= 3) {
            $numAttempts = rand(2, 3);
            $willEventuallyPass = rand(1, 100) <= 70;
        } elseif ($performanceLevel <= 7) {
            $numAttempts = rand(1, 2);
            $willEventuallyPass = rand(1, 100) <= 90;
        } else {
            $numAttempts = 1;
            $willEventuallyPass = rand(1, 100) <= 98;
        }
        
        $bestScore = 0;
        $hasPassed = false;

        for ($attempt = 0; $attempt < $numAttempts; $attempt++) {
            $baseScore = $this->getQuizScore($performanceLevel, $attempt, $numAttempts, $willEventuallyPass);
            $score = min(100, max(0, $baseScore + rand(-5, 5)));
            
            $passed = $score >= $quiz->passing_score;
            
            if ($score > $bestScore) {
                $bestScore = $score;
            }
            
            if ($passed) {
                $hasPassed = true;
            }

            $answersData = $this->generateQuizAnswers($questions, $score);
            
            // ✅ NEW: Base time on difficulty and performance level
            $baseTime = $this->getQuizTimeByDifficulty($lesson->difficulty, $performanceLevel);
            // Struggling students take longer
            $timeVariation = $performanceLevel <= 3 ? rand(1.2, 1.5) : rand(0.8, 1.1);
            $completionTime = (int)($baseTime * $timeVariation);
            
            $attemptDay = $currentDay + ($attempt * rand(1, 3));
            $startedAt = now()->subDays($attemptDay)->subSeconds($completionTime);
            
            UserQuizAttempt::create([
                'user_id' => $student->id,
                'quiz_id' => $quiz->id,
                'started_at' => $startedAt,
                'completed_at' => $startedAt->copy()->addSeconds($completionTime),
                'completion_time' => $completionTime,
                'score' => $score,
                'passed' => $passed,
                'answers_data' => json_encode($answersData)
            ]);

            if ($passed && rand(1, 100) <= 95) {
                break;
            }
        }

        $studentLesson->best_score = $bestScore;
        $studentLesson->quiz_passed = $hasPassed;
        $studentLesson->save();
        
        return $hasPassed;
    }

    private function createSimulationAttempts($student, $lesson, $studentLesson, $performanceLevel, $currentDay): bool
    {
        $simConfigs = [
            1 => ['id' => 'lesson-1-sim', 'scenarios' => 5],
            2 => ['id' => 'lesson-2-sim', 'scenarios' => 5],
            3 => ['id' => 'lesson-3-sim', 'scenarios' => 5],
        ];

        $config = $simConfigs[$lesson->id] ?? null;
        if (!$config) {
            return true;
        }

        if ($performanceLevel <= 3) {
            $numAttempts = rand(2, 3);
            $willEventuallyPass = rand(1, 100) <= 75;
        } elseif ($performanceLevel <= 7) {
            $numAttempts = rand(1, 2);
            $willEventuallyPass = rand(1, 100) <= 92;
        } else {
            $numAttempts = 1;
            $willEventuallyPass = rand(1, 100) <= 98;
        }
        
        $hasPassed = false;

        for ($attempt = 1; $attempt <= $numAttempts; $attempt++) {
            if ($performanceLevel <= 3) {
                $baseSuccessRate = 0.50 + ($attempt * 0.12);
            } elseif ($performanceLevel <= 7) {
                $baseSuccessRate = 0.68 + ($attempt * 0.10);
            } else {
                $baseSuccessRate = 0.82;
            }
            
            if ($attempt === $numAttempts && $willEventuallyPass && !$hasPassed) {
                $baseSuccessRate = 0.80;
            }
            
            $scenarioResults = $this->generateSimulationResults($config['scenarios'], $baseSuccessRate, $attempt);
            
            $correctCount = collect($scenarioResults)->where('correct', true)->count();
            $percentage = ($correctCount / $config['scenarios']) * 100;
            $passed = $percentage >= 70;
            
            if ($passed) {
                $hasPassed = true;
            }

            $clickData = $this->generateClickData($config['scenarios']);
            
            // ✅ NEW: Base time on difficulty and performance level
            // Harder lessons take longer, and struggling students take more time per scenario
            $timePerScenario = $this->getSimTimeByDifficulty($lesson->difficulty, $performanceLevel);
            $timeTaken = $timePerScenario * $config['scenarios'];
            
            $attemptDay = $currentDay + ($attempt * rand(1, 2));
            $startedAt = now()->subDays($attemptDay);
            
            SimulationAttempt::create([
                'user_id' => $student->id,
                'lesson_id' => $lesson->id,
                'simulation_id' => $config['id'],
                'started_at' => $startedAt,
                'completed_at' => $startedAt->copy()->addSeconds($timeTaken),
                'score' => $correctCount,
                'total_scenarios' => $config['scenarios'],
                'time_taken' => $timeTaken,
                'click_data' => $clickData,
                'scenario_results' => $scenarioResults,
                'attempt_number' => $attempt
            ]);

            if ($passed && rand(1, 100) <= 90) {
                break;
            }
        }

        $studentLesson->simulations_completed = $hasPassed;
        $studentLesson->save();
        
        return $hasPassed;
    }

    // ✅ NEW HELPER METHODS

    private function getDifficultyMultiplier($difficulty): float
    {
        return match($difficulty) {
            'EASY' => 1.0,
            'MEDIUM' => 1.5,
            'HARD' => 2.0,
            default => 1.0,
        };
    }

    private function getQuizTimeByDifficulty($difficulty, $performanceLevel): int
    {
        // Base times in seconds
        $baseTimes = [
            'EASY' => 180,     // 3 minutes
            'MEDIUM' => 300,   // 5 minutes
            'HARD' => 480,     // 8 minutes
        ];

        $baseTime = $baseTimes[$difficulty] ?? 180;
        
        // Struggling students take 1.5-2x longer
        if ($performanceLevel <= 3) {
            $baseTime = (int)($baseTime * rand(150, 200) / 100);
        }
        // Average students take normal time
        elseif ($performanceLevel <= 7) {
            $baseTime = (int)($baseTime * rand(80, 120) / 100);
        }
        // Excellent students are faster
        else {
            $baseTime = (int)($baseTime * rand(60, 90) / 100);
        }

        return $baseTime;
    }

    private function getSimTimeByDifficulty($difficulty, $performanceLevel): int
    {
        // Time per scenario in seconds
        $timePerScenario = match($difficulty) {
            'EASY' => rand(30, 50),      // 30-50 seconds per scenario
            'MEDIUM' => rand(45, 75),    // 45-75 seconds per scenario
            'HARD' => rand(60, 90),      // 60-90 seconds per scenario
            default => rand(40, 60),
        };

        // Struggling students take longer
        if ($performanceLevel <= 3) {
            $timePerScenario = (int)($timePerScenario * 1.3);
        }
        // Excellent students are faster
        elseif ($performanceLevel >= 8) {
            $timePerScenario = (int)($timePerScenario * 0.7);
        }

        return $timePerScenario;
    }

    /**
     * Get progress percentage based on performance level
     */
    private function getProgressPercentage($performanceLevel): float
    {
        if ($performanceLevel <= 3) {
            return rand(20, 40) / 100; // Struggling: 20-40%
        } elseif ($performanceLevel <= 7) {
            return rand(50, 80) / 100; // Average: 50-80%
        } else {
            return rand(85, 100) / 100; // Excellent: 85-100%
        }
    }

    /**
     * Calculate quiz score based on performance and attempt number
     */
    private function getQuizScore($performanceLevel, $attemptNumber, $totalAttempts, $willEventuallyPass): int
    {
        // Base scores by performance level (higher now to ensure more passing)
        $baseScores = [
            1 => 55, 2 => 60, 3 => 65,  // Struggling: 55-65%
            4 => 72, 5 => 75, 6 => 78, 7 => 82,  // Average: 72-82%
            8 => 85, 9 => 90, 10 => 95  // Excellent: 85-95%
        ];
        
        $baseScore = $baseScores[$performanceLevel];
        
        // Improvement with each attempt (students learn)
        $improvement = ($attemptNumber * 8);
        
        $score = $baseScore + $improvement;
        
        // If this is the last attempt and they should pass, ensure they pass
        if ($attemptNumber === $totalAttempts - 1 && $willEventuallyPass && $score < 80) {
            $score = rand(80, 85);
        }
        
        return min(100, $score);
    }

    /**
     * Generate realistic quiz answers
     */
    private function generateQuizAnswers($questions, $targetScore): array
    {
        $totalQuestions = $questions->count();
        $targetCorrect = round(($targetScore / 100) * $totalQuestions);
        
        $answersData = [];
        $correctCount = 0;

        // Shuffle questions to randomize which ones are correct
        $questionsList = $questions->shuffle();

        foreach ($questionsList as $index => $question) {
            $correctAnswer = $question->answers->firstWhere('is_correct', true);
            
            // Determine if this answer should be correct
            $remainingQuestions = $totalQuestions - $index;
            $needCorrect = $targetCorrect - $correctCount;
            
            // If we need more correct answers and running out of questions, make it correct
            // Otherwise use probability based on how many we still need
            $shouldBeCorrect = $needCorrect > 0 && 
                             ($needCorrect >= $remainingQuestions || 
                              rand(1, $remainingQuestions) <= $needCorrect);
            
            $userAnswer = $shouldBeCorrect 
                ? $correctAnswer->option_letter 
                : $question->answers->where('is_correct', false)->random()->option_letter;
            
            $isCorrect = $userAnswer === $correctAnswer->option_letter;
            
            if ($isCorrect) {
                $correctCount++;
            }

            $answersData[] = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'user_answer' => $userAnswer,
                'correct_answer' => $correctAnswer->option_letter,
                'is_correct' => $isCorrect,
                'points' => $question->points,
                'earned_points' => $isCorrect ? $question->points : 0,
                'answers' => $question->answers->map(function($answer) {
                    return [
                        'option_letter' => $answer->option_letter,
                        'answer_text' => $answer->answer_text,
                        'is_correct' => $answer->is_correct,
                        'explanation' => $answer->explanation
                    ];
                })->toArray()
            ];
        }

        return $answersData;
    }

    /**
     * Generate simulation scenario results
     */
    private function generateSimulationResults($totalScenarios, $baseSuccessRate, $attemptNumber): array
    {
        $results = [];
        
        // Increase success rate with attempts (learning)
        $successRate = min(0.95, $baseSuccessRate + (($attemptNumber - 1) * 0.1));
        
        $scenarioNames = [
            'Phishing Email Detection',
            'Suspicious Link Identification',
            'Password Security',
            'Social Engineering Warning',
            'Data Privacy Protection'
        ];

        $actions = [
            'Report as Phishing',
            'Verify Sender',
            'Check URL',
            'Enable 2FA',
            'Ignore and Delete',
            'Contact IT Support',
            'Update Password',
            'Review Privacy Settings'
        ];

        for ($i = 0; $i < $totalScenarios; $i++) {
            $isCorrect = (rand(1, 100) / 100) <= $successRate;
            
            $results[] = [
                'scenario' => $scenarioNames[$i] ?? "Scenario " . ($i + 1),
                'correct' => $isCorrect,
                'selected_action' => $actions[array_rand($actions)]
            ];
        }

        return $results;
    }

    /**
     * Generate realistic click data
     */
    private function generateClickData($scenarios): array
    {
        $clickData = [];
        $totalClicks = rand(10, 30);
        
        $actions = [
            'clicked_element',
            'opened_action_menu',
            'selected_option',
            'closed_dialog',
            'viewed_hint'
        ];

        for ($i = 0; $i < $totalClicks; $i++) {
            $clickData[] = [
                'timestamp' => now()->subMinutes(rand(1, 10))->timestamp,
                'action' => $actions[array_rand($actions)],
                'scenario' => rand(1, $scenarios)
            ];
        }

        return $clickData;
    }
}
