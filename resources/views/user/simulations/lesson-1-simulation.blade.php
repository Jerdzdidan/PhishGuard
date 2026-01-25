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

    .sms-header {
        background: linear-gradient(135deg, #0066cc, #0052a3);
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
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

    .lock-icon.insecure {
        color: #dc3545;
    }

    .sms-contact {
        font-size: 16px;
        font-weight: 600;
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
        transform: scale(1.02);
    }

    .link-url {
        font-size: 11px;
        color: #0066cc;
        word-break: break-all;
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

    .warning-box {
        background: #fff3cd;
        border: 2px solid #ffc107;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
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

    .messenger-header {
        background: #0084ff;
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .messenger-messages {
        padding: 20px;
        background: white;
        min-height: calc(100% - 60px);
    }

    .messenger-bubble {
        background: #e4e6eb;
        padding: 10px 15px;
        border-radius: 18px;
        margin-bottom: 10px;
        max-width: 80%;
        display: inline-block;
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
                            <i class="ri-smartphone-line" style="font-size: 4rem; color: #696cff;"></i>
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

                        <h5 class="mb-4">Are you ready to start the simulation?</h5>
                        <button type="button" class="btn btn-primary btn-lg" id="startSimulationBtn">
                            <i class="ri-play-line me-2"></i> Start Simulation
                        </button>
                        <div class="mt-3">
                            <a href="{{ route('lessons.show', Crypt::encryptString($lesson->id)) }}" class="btn btn-label-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Back to Lesson
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
                                <span>9:41 AM</span>
                                <span>📶 5G 🔋 89%</span>
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
                                    <span class="mb-0 h6 text-primary">3. Simulation</span>
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
                <h6 class="mb-3">Simulation Information</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Scenarios</span>
                    <strong>{{ $simulation['total_scenarios'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Passing Score</span>
                    <strong>70%</strong>
                </div>
                <hr>
                <div class="alert alert-info mb-0">
                    <i class="ri-information-line me-2"></i>
                    <small>Complete the simulation to unlock the next lesson. You'll face {{ $simulation['total_scenarios'] }} real-world scenarios.</small>
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

    // 5 Diverse scenarios mixing SMS, fake websites, and marketplace scams
    const scenarios = [
        {
            name: "GCash Phishing SMS",
            type: "sms",
            render: () => `
                <div class="sms-header">
                    <span>←</span>
                    <div class="sms-contact">+639171234567</div>
                </div>
                <div class="sms-messages">
                    <div class="message received">
                        <div class="bubble">
                            <strong>GCASH ALERT:</strong><br><br>
                            Your GCash account will be LOCKED in 24 hours due to failed verification.
                            <br><br>
                            Para maiwasan ang suspension, i-verify agad dito:
                            <div class="link-preview" onclick="handleScenarioClick(0)">
                                <div style="font-weight: 600;">🔒 Secure Verification</div>
                                <div class="link-url">gcash-verify.ph/secure?id=PH28492</div>
                            </div>
                            <div class="message-time">Today 9:23 AM</div>
                        </div>
                    </div>
                </div>
            `,
            question: "You received this SMS. What do you do?",
            actions: [
                { text: "Click the link to verify my account", safe: false },
                { text: "Ignore and check the official GCash app directly", safe: true },
                { text: "Call the number back to confirm", safe: false },
                { text: "Reply to ask if it's legitimate", safe: false }
            ],
            feedback: {
                correct: "✅ SAFE! You avoided a phishing scam! The URL 'gcash-verify.ph' is NOT official GCash. Always check the official app for account issues.",
                incorrect: "❌ DANGER! This is a phishing scam. The URL is fake and designed to steal your credentials. Always verify through the official app."
            }
        },
        {
            name: "Fake BDO Login Page",
            type: "website",
            render: () => `
                <div class="browser-bar">
                    <div class="address-bar">
                        <span class="lock-icon insecure">🔓</span>
                        <span style="flex: 1;">http://bdo-online-secure.com/login</span>
                    </div>
                </div>
                <div class="login-page">
                    <div class="bank-logo">
                        <h1>🏦 BDO</h1>
                        <p style="color: #666; margin: 0;">Online Banking</p>
                    </div>
                    <div class="warning-box">
                        <strong>⚠️ SECURITY ALERT:</strong><br>
                        Unusual activity detected. Please verify your identity.
                    </div>
                    <div class="input-group">
                        <label>Username</label>
                        <input type="text" placeholder="Enter your username">
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" placeholder="Enter your password">
                    </div>
                    <button class="submit-btn" onclick="handleScenarioClick(1)">VERIFY NOW</button>
                </div>
            `,
            question: "You clicked an email link and this page loaded. What do you do?",
            actions: [
                { text: "Enter my login details to verify", safe: false },
                { text: "Close immediately - this looks fake!", safe: true },
                { text: "Check if it looks legitimate first", safe: false },
                { text: "Use it but change password after", safe: false }
            ],
            feedback: {
                correct: "✅ EXCELLENT! You spotted a fake website! The URL 'bdo-online-secure.com' is NOT BDO's real website. Real BDO uses 'online.bdo.com.ph' with HTTPS.",
                incorrect: "❌ COMPROMISED! This is a phishing site! Your credentials were stolen. Always check the URL carefully."
            }
        },
        {
            name: "Facebook Marketplace Scam",
            type: "messenger",
            render: () => `
                <div class="messenger-header">
                    <span>←</span>
                    <div style="flex: 1;">
                        <div style="font-weight: 600;">John Seller</div>
                        <div style="font-size: 12px; opacity: 0.9;">Active now</div>
                    </div>
                </div>
                <div class="messenger-messages">
                    <div style="clear: both;">
                        <div class="messenger-bubble">
                            iPhone 15 Pro - BRAND NEW<br>
                            ₱35,000 only!<br>
                            Original price: ₱89,990<br><br>
                            Need quick cash!<br>
                            Send 50% down payment to reserve
                        </div>
                    </div>
                    <div style="clear: both;">
                        <div class="messenger-bubble">
                            <div class="link-preview" onclick="handleScenarioClick(2)">
                                <div style="font-weight: 600;">💳 Send Payment</div>
                                <div class="link-url">GCash: 09171234567</div>
                            </div>
                        </div>
                    </div>
                </div>
            `,
            question: "This deal seems too good to be true. What do you do?",
            actions: [
                { text: "Send the down payment immediately before someone else gets it", safe: false },
                { text: "Insist on meetup only, no advance payment", safe: true },
                { text: "Send only 25% as a compromise", safe: false },
                { text: "Ask for more photos to verify", safe: false }
            ],
            feedback: {
                correct: "✅ SMART! Never send advance payments to strangers. Legitimate sellers accept payment upon meetup and inspection only.",
                incorrect: "❌ SCAMMED! You just lost money. The seller will block you and disappear. Never send advance payments!"
            }
        },
        {
            name: "Job Offer Training Fee Scam",
            type: "sms",
            render: () => `
                <div class="sms-header" style="background: linear-gradient(135deg, #0077b5, #005582);">
                    <span>←</span>
                    <div class="sms-contact">HR Department</div>
                </div>
                <div class="sms-messages">
                    <div class="message received">
                        <div class="bubble">
                            <strong>CONGRATULATIONS!</strong><br><br>
                            You've been selected for:<br>
                            📋 Data Entry Specialist<br>
                            💰 ₱60,000/month<br>
                            🏠 Work from home<br><br>
                            Pay ₱2,500 training fee to start:
                            <div class="link-preview" onclick="handleScenarioClick(3)">
                                <div style="font-weight: 600;">💼 Start Your Career</div>
                                <div class="link-url">careers-ph.com/apply</div>
                            </div>
                            <div class="message-time">Today 2:45 PM</div>
                        </div>
                    </div>
                </div>
            `,
            question: "You don't remember applying, but this job looks great. What do you do?",
            actions: [
                { text: "Pay the training fee to secure the position", safe: false },
                { text: "Research the company and verify legitimacy first", safe: true },
                { text: "Send ID copies to secure the position", safe: false },
                { text: "Share the opportunity with friends", safe: false }
            ],
            feedback: {
                correct: "✅ WISE! Legitimate employers NEVER charge application or training fees. Always research companies thoroughly before sharing personal info or paying.",
                incorrect: "❌ VICTIMIZED! This is a job scam. You lost ₱2,500 and possibly shared personal information with criminals."
            }
        },
        {
            name: "Public WiFi Banking Risk",
            type: "sms",
            render: () => `
                <div class="sms-header" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <span>←</span>
                    <div class="sms-contact">WiFi Notification</div>
                </div>
                <div class="sms-messages">
                    <div class="message received">
                        <div class="bubble">
                            📶 <strong>Free_Coffee_Shop_WiFi</strong><br>
                            No password required<br>
                            Open network - Click to connect<br><br>
                            <em>Situation: You urgently need to pay your credit card bill online to avoid late fees.</em>
                            <div class="link-preview" onclick="handleScenarioClick(4)">
                                <div style="font-weight: 600;">📶 Connect to WiFi</div>
                            </div>
                            <div class="message-time">Today 4:00 PM</div>
                        </div>
                    </div>
                </div>
            `,
            question: "You need to do urgent online banking. What's the SAFEST option?",
            actions: [
                { text: "Connect to the free WiFi and do banking quickly", safe: false },
                { text: "Use my mobile data instead for banking", safe: true },
                { text: "Use incognito/private browsing mode for safety", safe: false },
                { text: "Connect but change my password immediately after", safe: false }
            ],
            feedback: {
                correct: "✅ SECURE! Using mobile data is the safest option for banking transactions. Public WiFi can expose your credentials to hackers on the same network.",
                incorrect: "❌ INTERCEPTED! Your banking credentials and credit card details were captured on the unsecured network. Never do banking or enter sensitive info on public WiFi!"
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
            data: {
                _token: '{{ csrf_token() }}',
                attempt_id: attemptId,
                score: score,
                time_taken: timeTaken,
                click_data: clickData,
                scenario_results: scenarioResults
            },
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