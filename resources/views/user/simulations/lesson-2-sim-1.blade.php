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

    .email-header {
        background: linear-gradient(135deg, #0077b5, #005582);
        color: white;
        padding: 15px 20px;
    }

    .email-subject {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .email-from {
        font-size: 13px;
        opacity: 0.9;
    }

    .email-body {
        padding: 20px;
        line-height: 1.6;
    }

    .job-details {
        background: #f0f8ff;
        border-left: 4px solid #0077b5;
        padding: 15px;
        margin: 15px 0;
        border-radius: 5px;
    }

    .warning-sign {
        background: #fff3cd;
        border: 2px solid #ffc107;
        padding: 12px;
        border-radius: 8px;
        margin: 15px 0;
    }

    .cta-button {
        background: #28a745;
        color: white;
        padding: 15px 25px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        cursor: pointer;
        margin-top: 15px;
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
            name: "Unrealistic Salary Offer",
            render: () => `
                <div class="email-header">
                    <div class="email-subject">🎉 Congratulations! You're Hired!</div>
                    <div class="email-from">From: recruitment@globaltech-careers.com</div>
                </div>
                <div class="email-body">
                    <p>Dear Job Seeker,</p>
                    <p>We are pleased to inform you that you have been selected for the position of:</p>
                    
                    <div class="job-details">
                        <strong>📋 Position:</strong> Data Entry Specialist<br>
                        <strong>💰 Salary:</strong> ₱80,000 - ₱120,000 per month<br>
                        <strong>🏠 Work Setup:</strong> 100% Work From Home<br>
                        <strong>⏰ Hours:</strong> Flexible, part-time (3-4 hours/day)<br>
                        <strong>📚 Experience:</strong> No experience required!
                    </div>

                    <p><strong>Job Requirements:</strong></p>
                    <ul style="margin-left: 20px;">
                        <li>Computer with internet</li>
                        <li>Good English communication</li>
                        <li>Willing to start immediately</li>
                    </ul>

                    <p><strong>To secure your position, please pay the one-time training fee of ₱3,500 via GCash to:</strong><br>
                    09171234567 (GlobalTech HR)</p>

                    <button class="cta-button" onclick="showAction(0)">APPLY NOW - LIMITED SLOTS!</button>
                </div>
            `,
            question: "You don't remember applying. What are the red flags?",
            actions: [
                { text: "🚩 Unrealistic salary + asking for payment = SCAM", safe: true },
                { text: "Great opportunity! Pay the fee quickly", safe: false },
                { text: "High salary for data entry seems normal", safe: false },
                { text: "Training fees are standard practice", safe: false }
            ],
            feedback: {
                correct: "✅ EXCELLENT! Major red flags: (1) Unrealistic salary for simple work, (2) No interview required, (3) Asking for payment upfront. Legitimate employers NEVER charge fees!",
                incorrect: "❌ SCAMMED! You lost ₱3,500. No legitimate company charges training fees. ₱80k-120k for part-time data entry is impossible."
            }
        },
        {
            name: "Personal Information Harvesting",
            render: () => `
                <div class="email-header">
                    <div class="email-subject">Your Application Status - Final Step</div>
                    <div class="email-from">From: hr.department@careers-ph.net</div>
                </div>
                <div class="email-body">
                    <p>Hello Applicant,</p>
                    <p>Your initial screening has been approved! To proceed with your application, we need to complete your employee profile.</p>

                    <div class="job-details">
                        <strong>Position:</strong> Customer Service Representative<br>
                        <strong>Salary:</strong> ₱25,000/month + Benefits<br>
                        <strong>Company:</strong> International BPO Inc.
                    </div>

                    <p><strong>Please submit the following documents immediately:</strong></p>
                    <ul style="margin-left: 20px; margin-top: 10px;">
                        <li>✓ Valid Government ID (front and back)</li>
                        <li>✓ Birth Certificate</li>
                        <li>✓ Proof of Billing</li>
                        <li>✓ NBI Clearance</li>
                        <li>✓ SSS/TIN/PhilHealth/Pag-IBIG numbers</li>
                        <li>✓ Bank account details for salary</li>
                    </ul>

                    <div class="warning-sign">
                        ⚠️ <strong>URGENT:</strong> Submit within 24 hours or position will be given to next candidate!
                    </div>

                    <button class="cta-button" onclick="showAction(1)">SUBMIT DOCUMENTS NOW</button>
                </div>
            `,
            question: "They want extensive personal documents before interview. What do you do?",
            actions: [
                { text: "🚩 Too much personal info requested too early - SUSPICIOUS", safe: true },
                { text: "Submit all documents to secure position", safe: false },
                { text: "Normal requirement, they need to process me", safe: false },
                { text: "Submit partial documents first", safe: false }
            ],
            feedback: {
                correct: "✅ SMART! Requesting extensive personal documents (IDs, bank details, SSS/TIN) BEFORE interview is identity theft! Legitimate companies verify identity AFTER hiring.",
                incorrect: "❌ IDENTITY THEFT! Scammers now have everything to: open bank accounts in your name, apply for loans, commit fraud. Never send IDs before interview!"
            }
        },
        {
            name: "Pyramid Scheme Disguised as Job",
            render: () => `
                <div class="email-header">
                    <div class="email-subject">💎 Be Your Own Boss - Unlimited Income!</div>
                    <div class="email-from">From: success@entrepreneurship-ph.com</div>
                </div>
                <div class="email-body">
                    <p>Hi Future Entrepreneur!</p>
                    <p>Tired of working 9-5 for minimal pay? Join our team and earn UNLIMITED income!</p>

                    <div class="job-details">
                        <strong>💼 What You'll Do:</strong><br>
                        • Recruit new members to our platform<br>
                        • Earn ₱5,000 per successful recruit<br>
                        • Build your own team of earners<br>
                        • Work from anywhere, anytime!
                    </div>

                    <p><strong>Earning Potential:</strong></p>
                    <ul style="margin-left: 20px;">
                        <li>Level 1 (You): Recruit 5 people = ₱25,000</li>
                        <li>Level 2 (Your recruits): They recruit 5 each = ₱125,000</li>
                        <li>Level 3 (Their recruits): 25 people each = ₱625,000/month!</li>
                    </ul>

                    <div class="warning-sign" style="background: #d4edda; border-color: #28a745;">
                        ✨ <strong>STARTER PACKAGE: Only ₱15,000</strong><br>
                        Includes training materials, website access, and starter kit!
                    </div>

                    <button class="cta-button" onclick="showAction(2)">JOIN NOW - LIMITED SLOTS!</button>
                </div>
            `,
            question: "This 'business opportunity' promises huge income. What is this?",
            actions: [
                { text: "🚩 Pyramid scheme - income from recruiting, not actual work", safe: true },
                { text: "Legitimate MLM business opportunity", safe: false },
                { text: "Good passive income strategy", safe: false },
                { text: "Worth trying with ₱15,000 investment", safe: false }
            ],
            feedback: {
                correct: "✅ CORRECT! This is a pyramid/ponzi scheme! Key indicators: (1) Pay to join, (2) Income from recruiting (not selling products/services), (3) Unrealistic earning projections, (4) Emphasis on 'levels' and 'downlines'.",
                incorrect: "❌ SCAMMED! You lost ₱15,000 joining an illegal pyramid scheme. You'll struggle to recruit anyone and won't earn back your investment. These collapse quickly!"
            }
        },
        {
            name: "Fake Remote Job - Invoice Scam",
            render: () => `
                <div class="email-header">
                    <div class="email-subject">Job Offer: Virtual Assistant Position</div>
                    <div class="email-from">From: hiring@remotecareer-global.com</div>
                </div>
                <div class="email-body">
                    <p>Dear Applicant,</p>
                    <p>We're offering you a Virtual Assistant position with our US-based company!</p>

                    <div class="job-details">
                        <strong>Position:</strong> Personal Virtual Assistant<br>
                        <strong>Salary:</strong> $800/month (₱45,000)<br>
                        <strong>Tasks:</strong> Email management, scheduling, light research<br>
                        <strong>Start Date:</strong> Immediate
                    </div>

                    <p><strong>First Assignment (Paid Immediately):</strong></p>
                    <p>We need you to purchase office supplies from this vendor for our Manila office. Use your personal funds first, then we'll reimburse + your first month salary.</p>

                    <div class="warning-sign">
                        <strong>Purchase List:</strong><br>
                        • Office chairs: ₱35,000<br>
                        • Laptops: ₱85,000<br>
                        • Printers: ₱25,000<br>
                        <br>
                        <strong>Total: ₱145,000</strong><br>
                        <em>Reimbursement + Salary (₱190,000) in 3 days!</em>
                    </div>

                    <button class="cta-button" onclick="showAction(3)">ACCEPT ASSIGNMENT</button>
                </div>
            `,
            question: "They want you to spend your own money first. What's happening?",
            actions: [
                { text: "🚩 Advance fee/reimbursement scam - I'll lose my money", safe: true },
                { text: "Good deal - ₱45k profit after reimbursement", safe: false },
                { text: "Company is testing my trustworthiness", safe: false },
                { text: "Buy cheaper items to reduce risk", safe: false }
            ],
            feedback: {
                correct: "✅ BRILLIANT! This is an 'advance fee' scam. You'll spend ₱145k, they disappear, and you're never reimbursed. Legitimate employers NEVER ask employees to front company expenses!",
                incorrect: "❌ MASSIVE LOSS! You just lost ₱145,000! The company is fake. They'll vanish after you send proof of purchase. This is a common scam targeting job seekers."
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