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
    }

    .address-bar input {
        border: none;
        outline: none;
        flex: 1;
        font-size: 13px;
    }

    .lock-icon {
        color: #888;
    }

    .lock-icon.secure {
        color: #28a745;
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
    }

    .warning-box {
        background: #fff3cd;
        border: 2px solid #ffc107;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .warning-box strong {
        color: #856404;
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
        margin-top: 10px;
    }

    .submit-btn:hover {
        background: #004a94;
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
            name: "Fake BDO Login Page",
            render: () => `
                <div class="browser-bar">
                    <div class="address-bar">
                        <span class="lock-icon insecure">🔓</span>
                        <input type="text" value="http://bdo-online-secure.com/login" readonly>
                    </div>
                </div>
                <div class="login-page">
                    <div class="bank-logo">
                        <h1>🏦 BDO</h1>
                        <p style="color: #666;">Online Banking</p>
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
                    <div class="input-group">
                        <label>Account Number</label>
                        <input type="text" placeholder="Enter account number">
                    </div>
                    <button class="submit-btn" onclick="showAction(0)">VERIFY NOW</button>
                </div>
            `,
            question: "You clicked an email link and this page loaded. What do you do?",
            actions: [
                { text: "Enter my login details to verify", safe: false },
                { text: "Close immediately - this is fake!", safe: true },
                { text: "Check if it looks legitimate first", safe: false },
                { text: "Use it but change password after", safe: false }
            ],
            feedback: {
                correct: "✅ EXCELLENT! You spotted a fake website! The URL 'bdo-online-secure.com' is NOT BDO's real website (online.bdo.com.ph). No HTTPS and asking for account number on login is suspicious.",
                incorrect: "❌ COMPROMISED! This is a phishing site! Your credentials were stolen. Real BDO is 'online.bdo.com.ph' with HTTPS."
            }
        },
        {
            name: "Suspicious Metrobank Site",
            render: () => `
                <div class="browser-bar">
                    <div class="address-bar">
                        <span class="lock-icon insecure">🔓</span>
                        <input type="text" value="http://metrobank-ph.net/secure" readonly>
                    </div>
                </div>
                <div class="login-page">
                    <div class="bank-logo">
                        <h1 style="color: #cc0000;">METROBANK</h1>
                        <p style="color: #666;">Secure Login Portal</p>
                    </div>
                    <div class="warning-box" style="background: #ffe6e6; border-color: #cc0000;">
                        <strong style="color: #cc0000;">🔒 URGENT SECURITY UPDATE:</strong><br>
                        Your account requires immediate verification to prevent suspension.
                    </div>
                    <div class="input-group">
                        <label>User ID</label>
                        <input type="text" placeholder="Enter your User ID">
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" placeholder="Enter your password">
                    </div>
                    <div class="input-group">
                        <label>Card Number</label>
                        <input type="text" placeholder="Enter full card number">
                    </div>
                    <div class="input-group">
                        <label>CVV</label>
                        <input type="text" placeholder="3-digit code">
                    </div>
                    <button class="submit-btn" style="background: #cc0000;" onclick="showAction(1)">VERIFY ACCOUNT</button>
                </div>
            `,
            question: "This page appeared after clicking an SMS link. What's wrong here?",
            actions: [
                { text: "The URL domain is suspicious", safe: true },
                { text: "Everything looks fine, proceed", safe: false },
                { text: "The urgent message seems genuine", safe: false },
                { text: "Fill it out to be safe", safe: false }
            ],
            feedback: {
                correct: "✅ CORRECT! 'metrobank-ph.net' is a fake domain. Real Metrobank uses 'onlinebanking.metrobank.com.ph'. Asking for CVV is a major red flag!",
                incorrect: "❌ DANGER! This is a phishing site. The domain is wrong, no HTTPS, and legitimate banks NEVER ask for CVV online."
            }
        },
        {
            name: "Landbank Impersonation",
            render: () => `
                <div class="browser-bar">
                    <div class="address-bar">
                        <span class="lock-icon insecure">🔓</span>
                        <input type="text" value="http://landbank-philippines.com/access" readonly>
                    </div>
                </div>
                <div class="login-page">
                    <div class="bank-logo">
                        <h1 style="color: #006837;">🏦 LANDBANK</h1>
                        <p style="color: #666;">Internet Banking</p>
                    </div>
                    <div class="warning-box">
                        <strong>⚠️ ACCOUNT VERIFICATION REQUIRED:</strong><br>
                        For your security, please verify your account details.
                    </div>
                    <div class="input-group">
                        <label>Username</label>
                        <input type="text" placeholder="Username">
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" placeholder="Password">
                    </div>
                    <div class="input-group">
                        <label>One-Time PIN (OTP)</label>
                        <input type="text" placeholder="Enter OTP from SMS">
                    </div>
                    <button class="submit-btn" style="background: #006837;" onclick="showAction(2)">LOGIN</button>
                    <p style="text-align: center; margin-top: 15px; font-size: 12px; color: #666;">
                        Secure Login - Protected by Landbank Security
                    </p>
                </div>
            `,
            question: "What makes this login page suspicious?",
            actions: [
                { text: "Login - it has security message", safe: false },
                { text: "Suspicious - asking for OTP upfront is wrong", safe: true },
                { text: "The logo looks official, proceed", safe: false },
                { text: "Use a different password to test", safe: false }
            ],
            feedback: {
                correct: "✅ SMART! Real banks generate OTP AFTER you login, not before. This is trying to steal your OTP in real-time to access your account!",
                incorrect: "❌ SCAMMED! This fake site will use your OTP immediately to drain your account. The domain is also wrong - real Landbank is 'ibanking.landbank.com'."
            }
        },
        {
            name: "Security Bank Fake Portal",
            render: () => `
                <div class="browser-bar">
                    <div class="address-bar">
                        <span class="lock-icon secure">🔒</span>
                        <input type="text" value="https://securitybank-online.net/login" readonly>
                    </div>
                </div>
                <div class="login-page">
                    <div class="bank-logo">
                        <h1 style="color: #0033a0;">Security Bank</h1>
                        <p style="color: #666;">Online Banking Portal</p>
                    </div>
                    <div class="input-group">
                        <label>Access ID</label>
                        <input type="text" placeholder="Your Access ID">
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" placeholder="Your Password">
                    </div>
                    <button class="submit-btn" style="background: #0033a0;" onclick="showAction(3)">SIGN IN</button>
                    <p style="text-align: center; margin-top: 15px; font-size: 12px; color: #666;">
                        © 2024 Security Bank. All rights reserved.
                    </p>
                </div>
            `,
            question: "This site has HTTPS (🔒). Is it safe?",
            actions: [
                { text: "Yes, HTTPS means it's safe", safe: false },
                { text: "No, check the domain first!", safe: true },
                { text: "Yes, the copyright notice is there", safe: false },
                { text: "Yes, it looks professional", safe: false }
            ],
            feedback: {
                correct: "✅ EXCELLENT! HTTPS doesn't guarantee legitimacy! The domain 'securitybank-online.net' is FAKE. Real Security Bank uses 'securitybank.com/online'.",
                incorrect: "❌ FOOLED! Scammers can get HTTPS certificates for fake domains. Always verify the DOMAIN NAME, not just the lock icon!"
            }
        },
        {
            name: "UnionBank Copycat",
            render: () => `
                <div class="browser-bar">
                    <div class="address-bar">
                        <span class="lock-icon insecure">🔓</span>
                        <input type="text" value="http://unionbank-verify.ph/account" readonly>
                    </div>
                </div>
                <div class="login-page">
                    <div class="bank-logo">
                        <h1 style="color: #e31837;">UnionBank</h1>
                        <p style="color: #666;">Account Verification Center</p>
                    </div>
                    <div class="warning-box" style="background: #ffe6e6; border-color: #e31837;">
                        <strong style="color: #e31837;">⚠️ IMPORTANT NOTICE:</strong><br>
                        Your account has been temporarily limited. Verify to restore access.
                    </div>
                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" placeholder="Your email">
                    </div>
                    <div class="input-group">
                        <label>Mobile Number</label>
                        <input type="tel" placeholder="09XX XXX XXXX">
                    </div>
                    <div class="input-group">
                        <label>ATM Card Number</label>
                        <input type="text" placeholder="16-digit card number">
                    </div>
                    <div class="input-group">
                        <label>PIN</label>
                        <input type="password" placeholder="4-digit PIN">
                    </div>
                    <button class="submit-btn" style="background: #e31837;" onclick="showAction(4)">VERIFY NOW</button>
                </div>
            `,
            question: "What are the MAJOR red flags here?",
            actions: [
                { text: "Asking for ATM PIN is NEVER legitimate", safe: true },
                { text: "It's okay, they need to verify me", safe: false },
                { text: "The urgent message is concerning but normal", safe: false },
                { text: "I should verify to restore my account", safe: false }
            ],
            feedback: {
                correct: "✅ PERFECT! Banks NEVER ask for your ATM PIN online! This is a critical red flag. Also, the domain 'unionbank-verify.ph' is fake (real: 'online.unionbankph.com').",
                incorrect: "❌ CRITICAL DANGER! You just gave criminals your PIN and card number! They can now withdraw all your money from ATMs. NEVER share your PIN!"
            }
        }
    ];

    // Start simulation
    startSimulation();

    function startSimulation() {
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