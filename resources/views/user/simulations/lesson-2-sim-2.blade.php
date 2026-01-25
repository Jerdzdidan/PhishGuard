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
        background: #f5f5f5;
    }

    .wifi-settings {
        background: white;
    }

    .setting-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 20px;
        text-align: center;
    }

    .wifi-list {
        padding: 15px;
    }

    .wifi-item {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .wifi-item:hover {
        border-color: #667eea;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .wifi-item.danger {
        border-color: #dc3545;
        background: #fff5f5;
    }

    .wifi-item.warning {
        border-color: #ffc107;
        background: #fffbf0;
    }

    .wifi-item.safe {
        border-color: #28a745;
        background: #f0fff4;
    }

    .wifi-name {
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 5px;
    }

    .wifi-security {
        font-size: 13px;
        color: #666;
    }

    .wifi-signal {
        float: right;
        font-size: 20px;
    }

    .task-description {
        background: #fff3cd;
        border: 2px solid #ffc107;
        padding: 15px;
        margin: 15px;
        border-radius: 10px;
        text-align: center;
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
        font-size: 20px;
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
            name: "Coffee Shop Free WiFi",
            render: () => `
                <div class="setting-header">
                    <h2>📶 Available WiFi Networks</h2>
                    <p style="font-size: 14px; opacity: 0.9;">Select a network to connect</p>
                </div>
                <div class="task-description">
                    <strong>⚠️ TASK:</strong> You need to pay your credit card bill online urgently. Which WiFi should you use?
                </div>
                <div class="wifi-list">
                    <div class="wifi-item danger" onclick="showAction(0, 0)">
                        <div class="wifi-signal">📶</div>
                        <div class="wifi-name">🔓 FREE_COFFEE_WIFI</div>
                        <div class="wifi-security">Open Network - No Password</div>
                    </div>

                    <div class="wifi-item warning" onclick="showAction(0, 1)">
                        <div class="wifi-signal">📶📶</div>
                        <div class="wifi-name">🔒 Starbucks_Guest</div>
                        <div class="wifi-security">Secured - Ask staff for password</div>
                    </div>

                    <div class="wifi-item" onclick="showAction(0, 2)">
                        <div class="wifi-signal">📶📶📶</div>
                        <div class="wifi-name">🔒 SM_Mall_Public_WiFi</div>
                        <div class="wifi-security">Secured - Login via portal required</div>
                    </div>

                    <div class="wifi-item safe" onclick="showAction(0, 3)">
                        <div class="wifi-signal">📶📶📶📶</div>
                        <div class="wifi-name">📱 My Phone (Mobile Data)</div>
                        <div class="wifi-security">Your personal hotspot</div>
                    </div>
                </div>
            `,
            question: "You need to do online banking urgently. Which network is SAFEST?",
            actions: [
                { text: "Free coffee WiFi - fastest", safe: false },
                { text: "Starbucks Guest - has password", safe: false },
                { text: "SM Mall WiFi - seems official", safe: false },
                { text: "My Mobile Data - most secure", safe: true }
            ],
            feedback: {
                correct: "✅ PERFECT! Your mobile data is the ONLY safe option for banking! All public WiFi (even password-protected) can be monitored. Hackers use WiFi to steal banking credentials, credit card details, and passwords.", incorrect: "❌ COMPROMISED! Your banking credentials were intercepted on the public network! Hackers on the same WiFi can capture your login details, card numbers, and OTPs. NEVER do banking on public WiFi!"
            }
        },
    {
name: "Airport WiFi Trap",
render: () => `
<div class="setting-header">
<h2>✈️ NAIA Terminal 3 WiFi</h2>
<p style="font-size: 14px; opacity: 0.9;">Choose carefully</p>
</div>
<div class="task-description">
<strong>Situation:</strong> Your flight is delayed 3 hours. You want to shop online while waiting.
</div>
<div class="wifi-list">
<div class="wifi-item" onclick="showAction(1, 0)">
<div class="wifi-signal">📶📶📶📶</div>
<div class="wifi-name">🔓 NAIA_Terminal3_FREE</div>
<div class="wifi-security">Open Network - No Password</div>
</div>
<div class="wifi-item danger" onclick="showAction(1, 1)">
                    <div class="wifi-signal">📶📶📶📶</div>
                    <div class="wifi-name">🔓 NAIA-FREE-WIFI-FAST</div>
                    <div class="wifi-security">Open Network - High Speed!</div>
                </div>

                <div class="wifi-item" onclick="showAction(1, 2)">
                    <div class="wifi-signal">📶📶📶</div>
                    <div class="wifi-name">🔒 Airport_Premium_WiFi</div>
                    <div class="wifi-security">Secured - ₱150/hour</div>
                </div>

                <div class="wifi-item" onclick="showAction(1, 3)">
                    <div class="wifi-signal">📶📶📶</div>
                    <div class="wifi-name">📱 Wait for Hotel WiFi</div>
                    <div class="wifi-security">Shop when you reach your destination</div>
                </div>
            </div>
        `,
        question: "You want to shop online using your credit card. Best choice?",
        actions: [
            { text: "Official NAIA terminal WiFi", safe: false },
            { text: "Fast free WiFi option", safe: false },
            { text: "Premium paid WiFi is safer", safe: false },
            { text: "Wait - don't shop on ANY airport WiFi", safe: true }
        ],
        feedback: {
            correct: "✅ SMART! Airports are hotspots for hackers! Even 'official' and 'premium' WiFi can be compromised or faked. 'NAIA-FREE-WIFI-FAST' is likely a fake access point set up by hackers. Wait until you're on a trusted private network!",
            incorrect: "❌ CARD STOLEN! Hackers commonly set up fake WiFi access points at airports to steal credit card info from shoppers. Your card details are now being used for fraudulent purchases!"
        }
    },
    {
        name: "Hotel WiFi vs VPN",
        render: () => `
            <div class="setting-header">
                <h2>🏨 Hotel Room WiFi</h2>
                <p style="font-size: 14px; opacity: 0.9;">Connected to: GrandHotel_Guest_5G</p>
            </div>
            <div class="task-description">
                <strong>Task:</strong> You need to access your company's confidential files and submit an urgent report.
            </div>
            <div class="wifi-list">
                <div class="wifi-item danger" onclick="showAction(2, 0)">
                    <div style="padding: 10px;">
                        <strong>Option 1:</strong><br>
                        Connect directly and access company portal<br>
                        <small>• Quick and easy</small>
                    </div>
                </div>

                <div class="wifi-item safe" onclick="showAction(2, 1)">
                    <div style="padding: 10px;">
                        <strong>Option 2:</strong><br>
                        Enable VPN first, then access company portal<br>
                        <small>• Extra security layer (encrypts data)</small>
                    </div>
                </div>

                <div class="wifi-item warning" onclick="showAction(2, 2)">
                    <div style="padding: 10px;">
                        <strong>Option 3:</strong><br>
                        Use Incognito/Private browsing mode<br>
                        <small>• Hides browsing history</small>
                    </div>
                </div>

                <div class="wifi-item" onclick="showAction(2, 3)">
                    <div style="padding: 10px;">
                        <strong>Option 4:</strong><br>
                        Ask hotel for password to 'secure' network<br>
                        <small>• Different from guest WiFi</small>
                    </div>
                </div>
            </div>
        `,
        question: "How should you safely access confidential company files?",
        actions: [
            { text: "Connect directly - hotel WiFi seems safe", safe: false },
            { text: "Use VPN to encrypt all traffic", safe: true },
            { text: "Incognito mode provides protection", safe: false },
            { text: "Hotel's 'secure' network is better", safe: false }
        ],
        feedback: {
            correct: "✅ EXCELLENT! VPN (Virtual Private Network) creates an encrypted tunnel for your data, protecting it even on public WiFi! This is ESSENTIAL when accessing confidential work data on ANY network outside your office!",
            incorrect: "❌ DATA BREACH! Company files exposed! Hotel WiFi (even 'secure' ones) can be monitored. Incognito mode only hides history from YOUR device, not from network snoopers. Your confidential data was intercepted!"
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

window.showAction = function(scenarioIndex, choice) {
    const scenario = scenarios[scenarioIndex];
    $('#action-question').text(scenario.question);
    
    const buttonsHTML = scenario.actions.map((action, index) => 
        `<button class="action-btn" onclick="selectAction(${scenarioIndex}, ${index})">${action.text}</button>`
    ).join('');
    
    $('#action-buttons').html(buttonsHTML);
    $('#action-overlay').addClass('active');

    clickData.push({
        scenario: scenarioIndex,
        choice: choice,
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