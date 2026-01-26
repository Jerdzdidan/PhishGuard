@extends('user.layout.base')

@section('title')
SIMULATION - {{ $lesson->title }}
@endsection

@section('nav_title')
SIMULATION - {{ $lesson->title }}
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('themes/sneat/assets/vendor/css/pages/app-academy.css') }}" />
<style>
    .phone-container {
        max-width: 400px;
        background: #000;
        border-radius: 40px;
        padding: 15px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
        margin: 0 auto;
    }

    .phone-screen {
        background: white;
        border-radius: 30px;
        overflow: hidden;
        height: 700px;
        position: relative;
    }

    .status-bar {
        background: #f8f9fa;
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        border-bottom: 1px solid #e0e0e0;
    }

    .screen-content {
        height: calc(100% - 40px);
        overflow-y: auto;
        background: #fff;
    }

    .email-app {
        height: 100%;
    }

    .email-list-header {
        background: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #e0e0e0;
        font-weight: 600;
    }

    .email-item {
        padding: 15px 20px;
        border-bottom: 1px solid #e0e0e0;
        cursor: pointer;
    }

    .email-sender {
        font-weight: 600;
        margin-bottom: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .email-subject {
        font-size: 14px;
        margin-bottom: 5px;
    }

    .email-preview {
        font-size: 13px;
        color: #666;
    }

    .attachment-badge {
        display: inline-block;
        background: #e3f2fd;
        color: #0066cc;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        margin-top: 5px;
    }

    .sms-header {
        background: linear-gradient(135deg, #0066cc, #0052a3);
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .sms-messages {
        padding: 20px;
        background: #f0f0f0;
        min-height: calc(100% - 60px);
    }

    .message.received .bubble {
        background: white;
        border-radius: 18px 18px 18px 4px;
        padding: 12px 16px;
        max-width: 85%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        display: inline-block;
    }

    .message-time {
        font-size: 11px;
        color: #888;
        margin-top: 5px;
    }

    .link-preview {
        background: #e3f2fd;
        border: 1px solid #90caf9;
        border-radius: 8px;
        padding: 10px;
        margin-top: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .link-preview:hover {
        background: #bbdefb;
    }

    .link-url {
        font-size: 11px;
        color: #0066cc;
        word-break: break-all;
    }

    .call-screen {
        background: linear-gradient(135deg, #28c76f, #1e8449);
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        padding: 40px 20px;
    }

    .caller-avatar {
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        margin-bottom: 20px;
    }

    .caller-name {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .caller-number {
        font-size: 16px;
        opacity: 0.8;
        margin-bottom: 30px;
    }

    .call-status {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 30px;
    }

    .call-message {
        background: rgba(255,255,255,0.2);
        padding: 20px;
        border-radius: 12px;
        margin: 20px 0;
        text-align: center;
        font-size: 14px;
    }

    .call-actions {
        display: flex;
        gap: 30px;
        margin-top: auto;
    }

    .call-btn {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        cursor: pointer;
        border: none;
    }

    .call-btn.answer {
        background: #fff;
        color: #28c76f;
    }

    .call-btn.decline {
        background: #dc3545;
    }

    .browser-bar {
        background: #f8f9fa;
        padding: 12px 15px;
        border-bottom: 1px solid #ddd;
    }

    .address-bar {
        background: white;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 8px 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
    }

    .lock-icon {
        color: #28c76f;
    }

    .lock-icon.insecure {
        color: #dc3545;
    }

    .login-page {
        padding: 30px 25px;
    }

    .bank-logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .bank-logo h1 {
        color: #005eb8;
        font-size: 32px;
        font-weight: bold;
        margin: 0;
    }

    .input-group {
        margin-bottom: 20px;
    }

    .input-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
    }

    .input-group input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }

    .submit-btn {
        width: 100%;
        padding: 15px;
        background: #005eb8;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }

    .action-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.9);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    }

    .action-overlay.active {
        display: flex;
    }

    .action-content {
        background: white;
        border-radius: 20px;
        padding: 30px;
        max-width: 500px;
        width: 100%;
    }

    .action-content h2 {
        color: #333;
        margin-bottom: 20px;
        font-size: 20px;
        text-align: center;
    }

    .action-buttons {
        display: grid;
        gap: 12px;
        margin-top: 25px;
    }

    .action-btn {
        padding: 15px 20px;
        border: 2px solid #ddd;
        background: white;
        border-radius: 10px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
        text-align: left;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        border-color: #696cff;
        background: #f8f9ff;
    }

    .timer {
        font-size: 1.5rem;
        font-weight: 600;
        color: #696cff;
    }

    .ready-screen {
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .warning-badge {
        display: inline-block;
        background: #dc3545;
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="row g-6">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <!-- Ready Screen -->
                <div id="readyScreen" class="ready-screen">
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="ri-mail-lock-line" style="font-size: 4rem; color: #696cff;"></i>
                        </div>
                        <h3 class="mb-3">{{ $simulation['title'] }}</h3>
                        <p class="text-muted mb-4">{{ $simulation['description'] }}</p>
                        
                        <div class="row justify-content-center mb-4">
                            <div class="col-md-6">
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="text-muted mb-1">Scenarios</h6>
                                    <h4 class="mb-0">{{ $simulation['total_scenarios'] }}</h4>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 mb-3">
                                    <h6 class="text-muted mb-1">Passing Score</h6>
                                    <h4 class="mb-0">70%</h4>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-4">Identify phishing, smishing, and vishing attacks!</h5>
                        <button type="button" class="btn btn-primary btn-lg" id="startSimulationBtn">
                            <i class="ri-play-line me-2"></i> Start Simulation
                        </button>
                        <div class="mt-3">
                            <a href="{{ route('lessons.simulations.index', Crypt::encryptString($lesson->id)) }}" class="btn btn-label-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Back to Simulations
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Simulation Screen -->
                <div id="simulationScreen" class="d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="mb-1">{{ $simulation['title'] }}</h4>
                            <p class="mb-0">Scenario <span id="scenarioNumber">1</span> of {{ $simulation['total_scenarios'] }}</p>
                        </div>
                        <div class="text-center">
                            <div class="timer" id="timer">00:00</div>
                            <small class="text-muted">Time Elapsed</small>
                        </div>
                    </div>

                    <!-- Phone Display -->
                    <div class="phone-container">
                        <div class="phone-screen">
                            <div class="status-bar">
                                <span>11:23 AM</span>
                                <span>📶 5G 🔋 75%</span>
                            </div>
                            <div class="screen-content" id="screen-content">
                                <!-- Scenarios render here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="accordion stick-top accordion-custom-button mb-4" id="courseContent">
            <div class="accordion-item active mb-0">
                <div class="accordion-header" id="headingOne">
                    <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#chapterOne" aria-expanded="true" aria-controls="chapterOne">
                        <span class="d-flex flex-column">
                            <span class="h5 mb-0">Lesson Content</span>
                            <span class="text-body fw-normal">{{ $lesson->time }} min</span>
                        </span>
                    </button>
                </div>
                <div id="chapterOne" class="accordion-collapse collapse show" data-bs-parent="#courseContent">
                    <div class="accordion-body py-4">
                        <div class="mb-4">
                            <a href="{{ route('lessons.show', Crypt::encryptString($lesson->id)) }}">
                                <label class="form-check-label ms-4">
                                    <span class="mb-0 h6">1. Lesson</span>
                                    <small class="text-body d-block">content</small>
                                </label>
                            </a>
                        </div>
                        @if ($lesson->quiz && $lesson->quiz->is_active)
                            <hr>
                            <div class="mb-4">
                                <a href="{{ route('lessons.quiz.show', Crypt::encryptString($lesson->id)) }}">
                                    <label class="form-check-label ms-4">
                                        <span class="mb-0 h6">2. Quiz</span>
                                        <small class="text-body d-block">assessment</small>
                                    </label>
                                </a>
                            </div>
                        @endif
                        @if ($lesson->has_simulation)
                            <hr>
                            <div class="mb-4">
                                <label class="ms-4">
                                    <span class="mb-0 h6 text-primary">3. Simulations</span>
                                    <small class="text-body d-block">interactive practice</small>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card stick-top">
            <div class="card-body">
                <h6 class="mb-3">Simulation Focus</h6>
                <div class="alert alert-info mb-3">
                    <strong>Attack Types:</strong>
                    <ul class="mb-0 mt-2" style="padding-left: 20px;">
                        <li>📧 Phishing (Email)</li>
                        <li>📱 Smishing (SMS)</li>
                        <li>📞 Vishing (Voice)</li>
                    </ul>
                </div>
                <div class="alert alert-warning mb-0">
                    <i class="ri-lightbulb-line me-2"></i>
                    <small><strong>Look for:</strong> Urgency, suspicious links, OTP/password requests, caller pressure</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Overlay -->
<div class="action-overlay" id="action-overlay">
    <div class="action-content">
        <h2 id="action-question">What do you do?</h2>
        <div class="action-buttons" id="action-buttons"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentScenario = 0;
    let score = 0;
    let startTime;
    let timerInterval;
    let attemptId = null;
    let clickData = [];
    let scenarioResults = [];

    // 5 Scenarios covering phishing, smishing, and vishing
    const scenarios = [
        {
            name: "Phishing - Fake Bank Email with Attachment",
            type: "email",
            render: () => `
                <div class="email-app">
                    <div class="email-list-header">Inbox</div>
                    <div class="email-item" onclick="handleScenarioClick(0)">
                        <div class="email-sender">
                            <span>Security Team - BPI</span>
                            <span class="warning-badge">URGENT</span>
                        </div>
                        <div class="email-subject">
                            <strong>Security Alert: Unusual Login Detected</strong>
                        </div>
                        <div class="email-preview">
                            We detected an unauthorized login attempt on your account from an unrecognized device. Please review the attached security report...
                        </div>
                        <div>
                            <span class="attachment-badge">📎 Security_Report.pdf.exe (2.3 MB)</span>
                        </div>
                    </div>
                    <div style="padding: 20px; background: #fff3cd; border-top: 2px solid #ffc107; text-align: center;">
                        <p style="margin: 0; font-size: 13px; color: #856404;">
                            <strong>⚠️ Tap the email above to open and view details</strong>
                        </p>
                    </div>
                </div>
            `,
            question: "You received this email in your inbox. What should you do?",
            actions: [
                { text: "Open the email and download the attachment to check", safe: false },
                { text: "Delete immediately - suspicious attachment and urgency", safe: true },
                { text: "Reply to ask if it's legitimate", safe: false },
                { text: "Forward to IT to scan the attachment first", safe: false }
            ],
            feedback: {
                correct: "✅ SAFE! You spotted a phishing email! Red flags: 'Security_Report.pdf.exe' is malware (double extension), urgency tactics, and unsolicited security warnings. Real banks never send security reports as attachments.",
                incorrect: "❌ INFECTED! This is phishing with a malicious attachment. The file 'Security_Report.pdf.exe' is malware disguised as a PDF. The .exe extension reveals it's a program that would install malware on your device. Banks never send executable files."
            }
        },
        {
            name: "Smishing - Delivery Scam with Shortened Link",
            type: "sms",
            render: () => `
                <div class="sms-header">
                    <span>←</span>
                    <div class="sms-contact">+639551234567</div>
                </div>
                <div class="sms-messages">
                    <div class="message received">
                        <div class="bubble">
                            <strong>LBC Delivery Update</strong><br><br>
                            Your parcel #LBC9284792 is out for delivery but requires verification.<br><br>
                            Failed delivery attempt:<br>
                            Reason: Incomplete address<br><br>
                            Click here to update delivery details and avoid return to sender:
                            <div class="link-preview" onclick="handleScenarioClick(1)">
                                <div style="font-weight: 600;">📦 Confirm Delivery Details</div>
                                <div class="link-url">bit.ly/lbc-ph-2892</div>
                            </div>
                            <div style="font-size: 11px; color: #999; margin-top: 8px;">
                                Note: Link expires in 24 hours
                            </div>
                            <div class="message-time">Today 10:45 AM</div>
                        </div>
                    </div>
                </div>
            `,
            question: "You received this SMS but don't remember ordering anything. What do you do?",
            actions: [
                { text: "Click the link to check - might be a surprise gift", safe: false },
                { text: "Ignore and check the official LBC app/website directly", safe: true },
                { text: "Reply 'STOP' to unsubscribe", safe: false },
                { text: "Call the sender number to verify", safe: false }
            ],
            feedback: {
                correct: "✅ EXCELLENT! You avoided smishing! Red flags: shortened link (bit.ly), urgency (24-hour expiration), unknown sender, and you don't recall ordering. Real delivery companies use official tracking on their apps/websites, not shortened links in SMS.",
                incorrect: "❌ COMPROMISED! This is smishing (SMS phishing). The shortened link 'bit.ly' hides the real destination, which is likely a fake page to steal your personal info or payment details. LBC uses official tracking through their website/app, not random shortened links."
            }
        },
        {
            name: "Vishing - Fake Bank Security Call",
            type: "call",
            render: () => `
                <div class="call-screen">
                    <div class="caller-avatar">🏦</div>
                    <div class="caller-name">BDO Security Department</div>
                    <div class="caller-number">+63 2 631 8000</div>
                    <div class="call-status">Incoming Call...</div>
                    
                    <div class="call-message">
                        <p style="margin: 0 0 15px 0; font-weight: 600;">⚠️ CALL PREVIEW</p>
                        <p style="margin: 0; font-size: 13px; line-height: 1.5;">
                            "Good morning, this is Officer Rodriguez from BDO Security. We detected suspicious transactions on your account ending in 4728. To prevent unauthorized withdrawals, we need to verify your identity immediately. Please prepare your account number, expiry date, and CVV for security verification."
                        </p>
                    </div>
                    
                    <div style="background: rgba(255,255,255,0.15); padding: 12px; border-radius: 8px; margin: 15px 0; font-size: 12px;">
                        <p style="margin: 0;">
                            <strong>Number appears to match:</strong><br>
                            BDO Customer Service (Spoofed)
                        </p>
                    </div>
                    
                    <div class="call-actions" onclick="handleScenarioClick(2)">
                        <button class="call-btn answer">📞</button>
                        <button class="call-btn decline">✖</button>
                    </div>
                </div>
            `,
            question: "This call appears to be from BDO's official number. What do you do?",
            actions: [
                { text: "Answer and provide verification details", safe: false },
                { text: "Answer but only give partial information", safe: false },
                { text: "Decline, then call BDO's official hotline myself", safe: true },
                { text: "Answer and ask them to call back later", safe: false }
            ],
            feedback: {
                correct: "✅ PROTECTED! You recognized vishing (voice phishing)! Even though the number appears legitimate (caller ID spoofing), real banks NEVER ask for CVV, full card numbers, or OTPs over the phone. Always hang up and call the official number yourself using the number on your card or bank's website.",
                incorrect: "❌ VICTIMIZED! This is vishing with caller ID spoofing. The attacker made it appear they're calling from BDO's real number, but banks NEVER request CVV, card numbers, or OTPs over the phone. You would have given away enough info for the scammer to drain your account."
            }
        },
        {
            name: "Phishing - Fake Login Page",
            type: "website",
            render: () => `
                <div class="browser-bar">
                    <div class="address-bar">
                        <span class="lock-icon">🔒</span>
                        <span style="flex: 1;">https://unionbank-online-ph.com/login</span>
                    </div>
                </div>
                <div class="login-page">
                    <div class="bank-logo">
                        <h1 style="color: #e31e24;">UnionBank</h1>
                        <p style="color: #666; margin: 0;">Online Banking</p>
                    </div>
                    
                    <div style="background: #fff3cd; border: 2px solid #ffc107; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <p style="margin: 0; font-weight: 600; color: #856404;">                
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" placeholder="Enter username">
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" placeholder="Enter password">
                </div>
                <div class="input-group">
                    <label>One-Time Password (OTP)</label>
                    <input type="text" placeholder="Enter 6-digit OTP">
                </div>
                
                <button class="submit-btn" onclick="handleScenarioClick(3)">
                    LOGIN & VERIFY
                </button>
                
                <p style="text-align: center; margin-top: 15px; font-size: 12px; color: #666;">
                    Secure Connection • Protected by SSL
                </p>
            </div>
        `,
        question: "You clicked an email link and this login page loaded. What do you do?",
        actions: [
            { text: "Close immediately and check the URL carefully", safe: true },
            { text: "Login - looks secure with SSL lock icon", safe: false },
            { text: "Enter username only to test if it's real", safe: false },
            { text: "Login using incognito mode for safety", safe: false }
        ],
        feedback: {
            correct: "✅ SMART! You spotted the phishing page! Red flags: URL 'unionbank-online-ph.com' is NOT UnionBank's real site (should be 'online.unionbank.com'), asking for OTP upfront (unusual), and 'session expired' pressure. The SSL lock doesn't mean it's safe - attackers can buy SSL certificates for fake sites!",
            incorrect: "❌ PHISHED! This fake login page stole your credentials and OTP! The URL 'unionbank-online-ph.com' looks similar but is NOT UnionBank's real website. Even with HTTPS/SSL (🔒), it's still a phishing site. Always manually type bank URLs or use saved bookmarks, never click email links."
        }
    },
    {
        name: "Smishing - OTP Verification Scam",
        type: "sms",
        render: () => `
            <div class="sms-header">
                <span>←</span>
                <div class="sms-contact">GCASH-ALERT</div>
            </div>
            <div class="sms-messages">
                <div class="message received">
                    <div class="bubble" onclick="handleScenarioClick(4)">
                        <strong>[GCASH] Security Alert</strong><br><br>
                        Your GCash account has been temporarily LOCKED due to suspicious activity detected.<br><br>
                        📍 Login Location: Cebu City<br>
                        📅 Time: 2:30 AM<br>
                        💰 Transaction Attempted: ₱25,000<br><br>
                        <span style="color: #dc3545; font-weight: 600;">IF THIS WAS NOT YOU:</span><br>
                        Verify your identity to prevent unauthorized access. You will receive an OTP shortly. Forward it to this number immediately or your account will be permanently locked within 2 hours.
                        <div style="background: #fff3cd; padding: 10px; border-radius: 6px; margin-top: 10px; font-size: 12px;">
                            ⚠️ <strong>TIME SENSITIVE:</strong> Respond within 2 hours
                        </div>
                        <div class="message-time">Today 11:05 AM</div>
                    </div>
                </div>
            </div>
        `,
        question: "You receive this alarming SMS. What's the correct action?",
        actions: [
            { text: "Wait for OTP and forward it as instructed", safe: false },
            { text: "Reply asking for more proof", safe: false },
            { text: "Call the sender to verify", safe: false },
            { text: "Ignore SMS and check the official GCash app", safe: true },
        ],
        feedback: {
            correct: "✅ PROTECTED! You avoided an OTP phishing scam! Red flags: asking you to FORWARD your OTP (huge red flag!), '2-hour deadline' urgency, suspicious activity claim at 2:30 AM. Real security alerts appear in the app, not via SMS asking for OTPs. NEVER share OTPs with anyone - GCash will never ask for them.",
            incorrect: "❌ ACCOUNT DRAINED! By forwarding your OTP, you gave the scammer access to drain your GCash account. This is SMS phishing (smishing). The scammer likely attempted to login to your account and triggered a real OTP, then tricked you into sending it to them. NEVER share OTPs - they're for your eyes only!"
        }
    }
];

// Start button click
$('#startSimulationBtn').on('click', function() {
    $('#readyScreen').addClass('d-none');
    $('#simulationScreen').removeClass('d-none');
    startSimulation();
});

function startSimulation() {
    $.ajax({
        url: '{{ route("lessons.simulations.start", ["id" => Crypt::encryptString($lesson->id), "simId" => $simulation["id"]]) }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            attemptId = response.attempt_id;
            startTime = Date.now();
            timerInterval = setInterval(updateTimer, 1000);
            renderScenario();
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to start simulation',
                confirmButtonColor: '#ea5455'
            }).then(() => {
                $('#simulationScreen').addClass('d-none');
                $('#readyScreen').removeClass('d-none');
            });
        }
    });
}

function updateTimer() {
    const elapsed = Math.floor((Date.now() - startTime) / 1000);
    const minutes = Math.floor(elapsed / 60);
    const seconds = elapsed % 60;
    $('#timer').text(
        String(minutes).padStart(2, '0') + ':' + 
        String(seconds).padStart(2, '0')
    );
}

function renderScenario() {
    $('#scenarioNumber').text(currentScenario + 1);
    $('#screen-content').html(scenarios[currentScenario].render());
}

window.handleScenarioClick = function(scenarioIndex) {
    const scenario = scenarios[scenarioIndex];
    $('#action-question').text(scenario.question);
    
    const buttonsHTML = scenario.actions.map((action, index) => 
        `<button class="action-btn" onclick="selectAction(${scenarioIndex}, ${index})">${action.text}</button>`
    ).join('');
    
    $('#action-buttons').html(buttonsHTML);
    $('#action-overlay').addClass('active');

    clickData.push({
        scenario: scenarioIndex,
        timestamp: Date.now() - startTime,
        action: 'opened_action_menu'
    });
};

window.selectAction = function(scenarioIndex, actionIndex) {
    const scenario = scenarios[scenarioIndex];
    const action = scenario.actions[actionIndex];
    
    $('#action-overlay').removeClass('active');

    clickData.push({
        scenario: scenarioIndex,
        action_selected: actionIndex,
        timestamp: Date.now() - startTime,
        safe: action.safe
    });

    scenarioResults.push({
        scenario: scenario.name,
        correct: action.safe,
        selected_action: action.text
    });

    if (action.safe) {
        score++;
        Swal.fire({
            icon: 'success',
            title: 'Correct!',
            html: scenario.feedback.correct,
            confirmButtonColor: '#28c76f',
        }).then(() => {
            nextScenario();
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Incorrect!',
            html: scenario.feedback.incorrect,
            confirmButtonColor: '#ea5455',
        }).then(() => {
            nextScenario();
        });
    }
};

function nextScenario() {
    currentScenario++;
    if (currentScenario < scenarios.length) {
        renderScenario();
    } else {
        finishSimulation();
    }
}

function finishSimulation() {
    clearInterval(timerInterval);
    const timeTaken = Math.floor((Date.now() - startTime) / 1000);
    
    $.ajax({
        url: '{{ route("lessons.simulations.submit", ["id" => Crypt::encryptString($lesson->id), "simId" => $simulation["id"]]) }}',
        type: 'POST',
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify({
            _token: '{{ csrf_token() }}',
            attempt_id: attemptId,
            score: score,
            time_taken: timeTaken,
            click_data: clickData,
            scenario_results: scenarioResults
        }),
        success: function(response) {
            if (response.success) {
                window.location.href = response.redirect_url;
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to submit simulation',
                confirmButtonColor: '#ea5455'
            });
        }
    });
}

// Prevent accidental page exit
let simulationInProgress = false;
$('#startSimulationBtn').on('click', function() {
    simulationInProgress = true;
});

window.addEventListener('beforeunload', function(e) {
    if (simulationInProgress && !$('#simulationScreen').hasClass('d-none')) {
        e.preventDefault();
        e.returnValue = '';
    }
});
});
</script>
@endsection