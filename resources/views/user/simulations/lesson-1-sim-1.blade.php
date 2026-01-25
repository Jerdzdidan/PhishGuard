@extends('user.layout.base')

@section('title')
{{ $simulation['title'] }} - {{ $lesson->title }}
@endsection

@section('nav_title')
{{ $simulation['title'] }}
@endsection

@section('style')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .simulation-container {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        min-height: 100vh;
        padding: 20px;
    }

    .phone-container {
        max-width: 400px;
        margin: 0 auto;
        background: #000;
        border-radius: 40px;
        padding: 15px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
    }

    .phone-screen {
        background: white;
        border-radius: 30px;
        overflow: hidden;
        height: 750px;
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

    /* SMS Interface */
    .sms-header {
        background: linear-gradient(135deg, #0066cc, #0052a3);
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .sms-contact {
        font-size: 16px;
        font-weight: 600;
    }

    .sms-messages {
        padding: 20px;
        background: #f0f0f0;
        min-height: calc(100% - 140px);
    }

    .message {
        margin-bottom: 15px;
        display: flex;
        gap: 10px;
    }

    .message.received .bubble {
        background: white;
        border-radius: 18px 18px 18px 4px;
        padding: 12px 16px;
        max-width: 85%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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

    .action-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
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
        text-align: center;
    }

    .action-content h2 {
        color: #333;
        margin-bottom: 15px;
    }

    .action-buttons {
        display: grid;
        gap: 15px;
        margin-top: 25px;
    }

    .action-btn {
        padding: 15px 25px;
        border: 2px solid #ddd;
        background: white;
        border-radius: 10px;
        cursor: pointer;
        font-size: 15px;
        transition: all 0.3s;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .timer {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        padding: 15px 25px;
        border-radius: 15px;
        font-weight: 600;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        color: #333;
        font-size: 1.2rem;
    }

    .scenario-counter {
        position: fixed;
        top: 20px;
        left: 20px;
        background: white;
        padding: 15px 25px;
        border-radius: 15px;
        font-weight: 600;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        color: #333;
    }
</style>
@endsection

@section('content')
<div class="simulation-container">
    <div class="scenario-counter">
        Scenario <span id="current-scenario">1</span> of {{ $simulation['total_scenarios'] }}
    </div>

    <div class="timer">
        Time: <span id="timer">00:00</span>
    </div>

    <div class="phone-container">
        <div class="phone-screen">
            <div class="status-bar">
                <span>9:41 AM</span>
                <span>📶 5G 🔋 89%</span>
            </div>
            <div class="screen-content" id="screen-content"></div>
        </div>
    </div>

    <div class="action-overlay" id="action-overlay">
        <div class="action-content">
            <h2 id="action-question">What do you do?</h2>
            <div class="action-buttons" id="action-buttons"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentScenario = 0;
    let score = 0;
    let startTime = Date.now();
    let attemptId = null;
    let clickData = [];
    let scenarioResults = [];

    const scenarios = [
        {
            name: "GCash Phishing SMS",
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
                            <div class="link-preview" onclick="showAction(0)">
                                <div>🔒 Secure Verification</div>
                                <div class="link-url">gcash-verify.ph/secure?id=PH28492</div>
                            </div>
                            <div class="message-time">Today 9:23 AM</div>
                        </div>
                    </div>
                </div>
            `,
            question: "You received this text. What do you do?",
            actions: [
                { text: "Click the link to verify", safe: false },
                { text: "Ignore and check GCash app directly", safe: true },
                { text: "Call the number back", safe: false },
                { text: "Reply to ask if it's legit", safe: false }
            ],
            feedback: {
                correct: "✅ SAFE! You avoided a phishing scam! The URL 'gcash-verify.ph' is NOT official. Always check the official GCash app for account issues.",
                incorrect: "❌ DANGER! This is a phishing scam. The URL is fake and designed to steal your credentials. Always verify through the official app."
            }
        },
        {
            name: "Fake Bank Email",
            render: () => `
                <div class="sms-header" style="background: linear-gradient(135deg, #c62828, #8e0000);">
                    <span>←</span>
                    <div class="sms-contact">BDO Security</div>
                </div>
                <div class="sms-messages">
                    <div class="message received">
                        <div class="bubble">
                            <strong>URGENT: Account Security Alert</strong><br><br>
                            We detected suspicious activity on your account.<br><br>
                            Click here to verify: bdo-secure-login.com/verify
                            <div class="link-preview" onclick="showAction(1)">
                                <div>🔐 Verify Account Now</div>
                                <div class="link-url">bdo-secure-login.com/verify</div>
                            </div>
                            <div class="message-time">Today 10:15 AM</div>
                        </div>
                    </div>
                </div>
            `,
            question: "This email looks urgent. What should you do?",
            actions: [
                { text: "Click the link immediately", safe: false },
                { text: "Call BDO's official hotline to verify", safe: true },
                { text: "Reply to the email asking for details", safe: false },
                { text: "Forward to friends to warn them", safe: false }
            ],
            feedback: {
                correct: "✅ EXCELLENT! Calling the official hotline is the safest way to verify. Never trust links in unexpected emails.",
                incorrect: "❌ COMPROMISED! This is a fake email. The domain 'bdo-secure-login.com' is not BDO's official website (online.bdo.com.ph)."
            }
        },
        {
            name: "Facebook Marketplace Scam",
            render: () => `
                <div class="sms-header" style="background: linear-gradient(135deg, #3b5998, #1e3a6f);">
                    <span>←</span>
                    <div class="sms-contact">John Seller</div>
                </div>
                <div class="sms-messages">
                    <div class="message received">
                        <div class="bubble">
                            iPhone 15 Pro - BRAND NEW<br>
                            ₱35,000 only! Original price ₱89,990<br><br>
                            Need quick cash, first to pay gets it!<br><br>
                            Send 50% down payment to reserve:
                            <div class="link-preview" onclick="showAction(2)">
                                <div>💳 GCash: 09171234567</div>
                            </div>
                            <div class="message-time">Today 11:30 AM</div>
                        </div>
                    </div>
                </div>
            `,
            question: "This deal seems too good to be true. What do you do?",
            actions: [
                { text: "Send the down payment immediately", safe: false },
                { text: "Insist on meetup only, no advance payment", safe: true },
                { text: "Send only 25% as compromise", safe: false },
                { text: "Ask for more photos", safe: false }
            ],
            feedback: {
                correct: "✅ SMART! Never send advance payments to strangers. Legitimate sellers accept payment upon meetup and inspection.",
                incorrect: "❌ SCAMMED! You lost your money. Never send advance payments for online purchases. This is a common scam tactic."
            }
        },
        {
            name: "Job Offer Scam",
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
                            Data Entry - ₱60,000/month<br>
                            Work from home<br><br>
                            Pay ₱2,500 training fee to start:
                            <div class="link-preview" onclick="showAction(3)">
                                <div>💼 Start Your Career</div>
                                <div class="link-url">careers-ph.com/apply</div>
                            </div>
                            <div class="message-time">Today 2:45 PM</div>
                        </div>
                    </div>
                </div>
            `,
            question: "You don't remember applying, but this job looks great. What do you do?",
            actions: [
                { text: "Pay the training fee immediately", safe: false },
                { text: "Research the company and verify legitimacy", safe: true },
                { text: "Send your ID copies to secure position", safe: false },
                { text: "Share the opportunity with friends", safe: false }
            ],
            feedback: {
                correct: "✅ WISE! Legitimate employers NEVER charge application or training fees. Always research companies thoroughly.",
                incorrect: "❌ VICTIMIZED! This is a job scam. You lost money and possibly shared personal information with criminals."
            }
        },
        {
            name: "Public WiFi Warning",
            render: () => `
                <div class="sms-header" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <span>←</span>
                    <div class="sms-contact">Free WiFi Connect</div>
                </div>
                <div class="sms-messages">
                    <div class="message received">
                        <div class="bubble">
                            🔓 Free_Coffee_Shop_WiFi<br>
                            No password required<br><br>
                            You need to pay your bills online.<br>
                            Connect to this WiFi?
                            <div class="link-preview" onclick="showAction(4)">
                                <div>📶 Connect Now</div>
                            </div>
                            <div class="message-time">Today 4:00 PM</div>
                        </div>
                    </div>
                </div>
            `,
            question: "You need to do urgent banking. What do you do?",
            actions: [
                { text: "Connect and do banking quickly", safe: false },
                { text: "Use mobile data instead", safe: true },
                { text: "Use incognito mode for safety", safe: false },
                { text: "Connect but change password after", safe: false }
            ],
            feedback: {
                correct: "✅ SECURE! Using mobile data is the safest option for banking. Public WiFi can expose your credentials to hackers.",
                incorrect: "❌ INTERCEPTED! Your banking credentials were captured on the unsecured network. Never do banking on public WiFi."
            }
        }
    ];

    // Start simulation
    startSimulation();

    function startSimulation() {
        // Call backend to create attempt
        $.ajax({
            url: '{{ route("lessons.simulations.start", ["id" => Crypt::encryptString($lesson->id), "simId" => $simulation["id"]]) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                attemptId = response.attempt_id;
                renderScenario();
                updateTimer();
            },
            error: function() {
                Swal.fire('Error', 'Failed to start simulation', 'error');
            }
        });
    }

    function updateTimer() {
        setInterval(function() {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            $('#timer').text(
                String(minutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0')
            );
        }, 1000);
    }

    function renderScenario() {
        $('#current-scenario').text(currentScenario + 1);
        $('#screen-content').html(scenarios[currentScenario].render());
    }

    window.showAction = function(scenarioIndex) {
        const scenario = scenarios[scenarioIndex];
        $('#action-question').text(scenario.question);
        
        const buttonsHTML = scenario.actions.map((action, index) => 
            `<button class="action-btn" onclick="selectAction(${scenarioIndex}, ${index})">${action.text}</button>`
        ).join('');
        
        $('#action-buttons').html(buttonsHTML);
        $('#action-overlay').addClass('active');

        // Track click
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

        // Track click
        clickData.push({
            scenario: scenarioIndex,
            action_selected: actionIndex,
            timestamp: Date.now() - startTime,
            safe: action.safe
        });

        // Record result
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
            error: function() {
                Swal.fire('Error', 'Failed to submit simulation', 'error');
            }
        });
    }
});
</script>
@endsection