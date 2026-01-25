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

    .content-area {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .scenario-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .password-input {
        width: 100%;
        padding: 15px;
        font-size: 16px;
        border: 2px solid #ddd;
        border-radius: 10px;
        margin: 15px 0;
        font-family: monospace;
    }

    .strength-meter {
        height: 10px;
        background: #e0e0e0;
        border-radius: 5px;
        margin: 10px 0;
        overflow: hidden;
    }

    .strength-bar {
        height: 100%;
        transition: all 0.3s;
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
        text-align: left;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .submit-btn {
        width: 100%;
        padding: 15px;
        background: #696cff;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 20px;
    }

    .info-box {
        background: #f0f8ff;
        border-left: 4px solid #696cff;
        padding: 15px;
        margin: 20px 0;
        border-radius: 5px;
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

    <div class="content-area" id="content-area"></div>

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
            name: "Creating a Strong Password",
            render: () => `
                <div class="scenario-header">
                    <h2>🔐 Create a Strong Password</h2>
                    <p>You're setting up a new password for your online banking. Which is the STRONGEST?</p>
                </div>

                <div class="info-box">
                    <strong>Password Requirements:</strong><br>
                    ✓ At least 12 characters<br>
                    ✓ Mix of uppercase & lowercase<br>
                    ✓ Numbers and symbols<br>
                    ✓ Hard to guess
                </div>

                <button class="submit-btn" onclick="showAction(0)">Evaluate Password Options</button>
            `,
            question: "Which password is the STRONGEST and most secure?",
            actions: [
                { text: "Password123!", safe: false },
                { text: "MariaGarcia2024!", safe: false },
                { text: "ilovemyfamily", safe: false },
                { text: "r9$mK#2pL@qX5nW", safe: true }
            ],
            feedback: {
                correct: "✅ EXCELLENT! This password is: (1) 15 characters long, (2) Random combination, (3) Has uppercase, lowercase, numbers & symbols, (4) Not based on personal info or dictionary words. Perfect!",
                incorrect: "❌ WEAK! Common mistakes: 'Password123!' is too predictable. 'MariaGarcia2024!' contains personal info. 'ilovemyfamily' is a common phrase. Use random characters!"
            }
        },
        {
            name: "Password Reuse Risk",
            render: () => `
                <div class="scenario-header">
                    <h2>♻️ Password Reuse Scenario</h2>
                    <p>You're creating passwords for multiple accounts</p>
                </div>

                <div class="info-box">
                    <strong>Your Accounts:</strong><br>
                    📧 Gmail: [Password needed]<br>
                    💰 BDO Online Banking: [Password needed]<br>
                    🛒 Shopee: [Password needed]<br>
                    📱 GCash: [Password needed]<br>
                    💼 Company Email: [Password needed]
                </div>

                <button class="submit-btn" onclick="showAction(1)">Choose Password Strategy</button>
            `,
            question: "What's the SAFEST way to manage these passwords?",
            actions: [
                { text: "Use the same strong password for everything - easier to remember", safe: false },
                { text: "Use 2 passwords: one for important accounts, one for shopping", safe: false },
                { text: "Use unique password for each account + password manager", safe: true },
                { text: "Use similar passwords with small variations (BDO2024!, Shopee2024!)", safe: false }
            ],
            feedback: {
                correct: "✅ PERFECT! Using unique passwords for each account with a password manager is the ONLY safe approach. If one account is breached, others remain secure. Password managers remember them all for you!",
                incorrect: "❌ DANGEROUS! If one account is hacked, ALL your accounts become vulnerable! Even 'variations' are easy to guess. One leaked password = all your accounts at risk!"
            }
        },
        {
            name: "Two-Factor Authentication",
            render: () => `
                <div class="scenario-header">
                    <h2>🔐 Enable Two-Factor Authentication (2FA)?</h2>
                    <p>Your bank offers 2FA for online banking</p>
                </div>

                <div class="info-box">
                    <strong>What is 2FA?</strong><br>
                    An extra security layer that requires:<br>
                    1️⃣ Your password (something you know)<br>
                    2️⃣ A code from your phone (something you have)<br>
                    <br>
                    <strong>Trade-off:</strong> Takes 10-15 seconds longer to log in
                </div>

                <button class="submit-btn" onclick="showAction(2)">Make Your Decision</button>
            `,
            question: "Should you enable 2FA for your online banking?",
            actions: [
                { text: "No - it's too inconvenient and slows me down", safe: false },
                { text: "No - my password is already strong enough", safe: false },
                { text: "Yes - ALWAYS enable 2FA for financial accounts!", safe: true },
                { text: "Maybe later - not urgent right now", safe: false }
            ],
            feedback: {
                correct: "✅ CRITICAL SECURITY! 2FA is your strongest defense! Even if hackers steal your password, they can't access your account without the 2FA code on YOUR phone. 15 seconds is worth protecting your money!",
                incorrect: "❌ HUGE RISK! Even the strongest password can be: phished, leaked in data breaches, or stolen by keyloggers. 2FA prevents 99.9% of automated hacking attempts. Your money deserves 15 extra seconds!"
            }
        },
        {
            name: "Password Manager Safety",
            render: () => `
                <div class="scenario-header">
                    <h2>🗝️ Using a Password Manager</h2>
                    <p>Your friend recommends using a password manager app</p>
                </div>

                <div class="info-box">
                    <strong>Password Manager Features:</strong><br>
                    ✓ Stores all your passwords securely<br>
                    ✓ Generates strong random passwords<br>
                    ✓ Auto-fills login forms<br>
                    ✓ Protected by one master password<br>
                    <br>
                    <strong>Question:</strong> Is it safe to put all passwords in one place?
                </div>

                <button class="submit-btn" onclick="showAction(3)">Evaluate the Risk</button>
            `,
            question: "Is using a password manager safe?",
            actions: [
                { text: "No - putting all passwords in one place is dangerous", safe: false },
                { text: "No - better to write them in a notebook", safe: false },
                { text: "Yes - IF you use a reputable manager with a STRONG master password", safe: true },
                { text: "No - I'll just remember them all instead", safe: false }
            ],
            feedback: {
                correct: "✅ SMART! Password managers (like Bitwarden, 1Password, LastPass) use military-grade encryption. Your passwords are safer there than: (1) Writing in notebook (can be lost/stolen), (2) Reusing weak passwords, (3) Trying to remember 50+ unique passwords. Master password = your only password to remember!",
                incorrect: "❌ MISCONCEPTION! Password managers are SAFER than alternatives because: (1) They use encryption, (2) Enable unique strong passwords for everything, (3) Prevent phishing (auto-fill only on correct sites), (4) No human can steal what they can't read!"
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
        $('#content-area').html(scenarios[currentScenario].render());
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