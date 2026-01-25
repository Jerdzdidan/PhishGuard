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

    .messenger-header {
        background: #0084ff;
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .messenger-avatar {
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .messenger-messages {
        padding: 20px;
        background: white;
        min-height: calc(100% - 140px);
    }

    .messenger-bubble {
        background: #e4e6eb;
        padding: 10px 15px;
        border-radius: 18px;
        margin-bottom: 10px;
        max-width: 80%;
        display: inline-block;
    }

    .messenger-bubble.sent {
        background: #0084ff;
        color: white;
        float: right;
        clear: both;
    }

    .messenger-image {
        max-width: 100%;
        border-radius: 10px;
        margin: 5px 0;
        background: #ddd;
        padding: 20px;
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

    .submit-btn {
        width: 100%;
        padding: 15px;
        background: #0084ff;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 20px;
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
            name: "Too Good To Be True Price",
            render: () => `
                <div class="messenger-header">
                    <span>←</span>
                    <div class="messenger-avatar">👤</div>
                    <div>
                        <div>TechDeals PH</div>
                        <div style="font-size: 12px; opacity: 0.9;">Active now</div>
                    </div>
                </div>
                <div class="messenger-messages">
                    <div style="clear: both;">
                        <div class="messenger-bubble">
                            iPhone 15 Pro Max 256GB<br>
                            BRAND NEW SEALED<br>
                            ₱35,000 only!<br>
                            Original: ₱89,990
                        </div>
                        <div style="font-size: 11px; color: #888; margin: 5px 0;">9:15 AM</div>
                    </div>
                    <div style="clear: both;">
                        <div class="messenger-bubble">
                            <div class="messenger-image">📱 [Product Photo]</div>
                        </div>
                    </div>
                    <div style="clear: both;">
                        <div class="messenger-bubble">
                            Rush sale! Need cash for emergency<br>
                            First come, first served!
                        </div>
                    </div>
                    <div style="clear: both; margin-top: 20px;">
                        <button class="submit-btn" onclick="showAction(0)">📝 Respond</button>
                    </div>
                </div>
            `,
            question: "This deal is 61% off. What's your first concern?",
            actions: [
                { text: "Price is suspiciously low", safe: true },
                { text: "Great deal! I should buy it", safe: false },
                { text: "Emergency sounds urgent, help them", safe: false },
                { text: "Ask for more photos", safe: false }
            ],
            feedback: {
                correct: "✅ SMART! 61% discount on brand new sealed items is extremely suspicious. It's likely fake, stolen, or a scam.",
                incorrect: "❌ RED FLAG MISSED! Extreme discounts (50%+ off) on new electronics are almost always scams or stolen goods."
            }
        },
        {
            name: "Advance Payment Request",
            render: () => `
                <div class="messenger-header">
                    <span>←</span>
                    <div class="messenger-avatar">👤</div>
                    <div>
                        <div>Gaming Laptop Seller</div>
                        <div style="font-size: 12px; opacity: 0.9;">Active 2m ago</div>
                    </div>
                </div>
                <div class="messenger-messages">
                    <div style="clear: both;">
                        <div class="messenger-bubble">
                            Gaming Laptop - RTX 4090<br>
                            ₱45,000 (worth ₱120,000)<br><br>
                            Many interested buyers!<br>
                            Need 50% deposit to reserve
                        </div>
                    </div>
                    <div style="clear: both;">
                        <div class="messenger-bubble">
                            Send to GCash: 09171234567<br>
                            Then meet at SM for COD balance
                        </div>
                    </div>
                    <div style="clear: both; margin-top: 20px;">
                        <button class="submit-btn" onclick="showAction(1)">📝 Respond</button>
                    </div>
                </div>
            `,
            question: "Seller wants 50% advance payment. What do you do?",
            actions: [
                { text: "Send deposit to secure the item", safe: false },
                { text: "Refuse - insist on full meetup only", safe: true },
                { text: "Negotiate to 25% deposit instead", safe: false },
                { text: "Send ₱1,000 test payment first", safe: false }
            ],
            feedback: {
                correct: "✅ EXCELLENT! Never send advance payments to strangers online. Legitimate sellers accept payment on meetup only.",
                incorrect: "❌ SCAMMED! You just lost ₱22,500. The seller will block you and disappear. Never send advance payments!"
            }
        },
        {
            name: "Urgency & Pressure Tactics",
            render: () => `
                <div class="messenger-header">
                    <span>←</span>
                    <div class="messenger-avatar">👤</div>
                    <div>
                        <div>PS5 Seller</div>
                        <div style="font-size: 12px; opacity: 0.9;">Active now</div>
                    </div>
                </div>
                <div class="messenger-messages">
                    <div style="clear: both;">
                        <div class="messenger-bubble">
                            PS5 + 5 Games - ₱18,000<br>
                            Brand new, sealed<br><br>
                            ⏰ LIMITED TIME OFFER!<br>
                            5 people messaging me now<br>
                            LAST STOCK!
                        </div>
                    </div>
                    <div style="clear: both;">
                        <div class="messenger-bubble">
                            Decide within 1 hour or I sell to others!<br>
                            Payment now = yours!
                        </div>
                    </div>
                    <div style="clear: both; margin-top: 20px;">
                        <button class="submit-btn" onclick="showAction(2)">📝 Respond</button>
                    </div>
                </div>
            `,
            question: "Seller is creating urgency and pressure. What's this tactic?",
            actions: [
                { text: "Classic scam pressure - walk away", safe: true },
                { text: "Act fast before it's gone!", safe: false },
                { text: "Negotiate but decide quickly", safe: false },
                { text: "Ask friends if they want it too", safe: false }
            ],
            feedback: {
                correct: "✅ WISE! Urgency and FOMO (Fear Of Missing Out) are classic scam tactics. Legitimate sellers don't pressure you.",
                incorrect: "❌ MANIPULATED! Scammers use urgency to make you decide emotionally, not rationally. Take your time!"
            }
        },
        {
            name: "No Meetup, Delivery Only",
            render: () => `
                <div class="messenger-header">
                    <span>←</span>
                    <div class="messenger-avatar">👤</div>
                    <div>
                        <div>Sneaker Store PH</div>
                        <div style="font-size: 12px; opacity: 0.9;">Active 5m ago</div>
                    </div>
                </div>
                <div class="messenger-messages">
                    <div style="clear: both;">
                        <div class="messenger-bubble">
                            Nike Jordan 1 - ₱8,500<br>
                            Authentic, with receipt<br><br>
                            Sorry, no meetups po<br>
                            Province kasi ako
                        </div>
                    </div>
                    <div style="clear: both;">
                        <div class="messenger-bubble">
                            Full payment + shipping (₱500)<br>
                            J&T/LBC COD available<br>
                            Guaranteed delivery 3-5 days
                        </div>
                    </div>
                    <div style="clear: both; margin-top: 20px;">
                        <button class="submit-btn" onclick="showAction(3)">📝 Respond</button>
                    </div>
                </div>
            `,
            question: "Seller refuses meetup, only delivery. What's the risk?",
            actions: [
                { text: "Can't inspect item = high risk", safe: true },
                { text: "COD sounds safe, proceed", safe: false },
                { text: "Ask for video call to verify", safe: false },
                { text: "Province sellers are usually legit", safe: false }
            ],
            feedback: {
                correct: "✅ CORRECT! Without meetup, you can't verify authenticity. Scammers send fakes or empty boxes even with COD.",
                incorrect: "❌ RISKY! COD doesn't guarantee authenticity. You might receive fake shoes and the seller disappears."
            }
        },
        {
            name: "Voucher/Payment Confirmation Scam",
            render: () => `
                <div class="messenger-header">
                    <span>←</span>
                    <div class="messenger-avatar">👤</div>
                    <div>
                        <div>Electronics Depot</div>
                        <div style="font-size: 12px; opacity: 0.9;">Active now</div>
                    </div>
                </div>
                <div class="messenger-messages">
                    <div style="clear: both;">
                        <div class="messenger-bubble">
                            MacBook Pro M3 - ₱55,000<br>
                            Original price: ₱145,000<br><br>
                            Ready to buy? I'll send voucher code<br>
                            You redeem it to confirm order
                        </div>
                    </div>
                    <div style="clear: both;">
                        <div class="messenger-bubble">
                            Here's the voucher link:<br>
                            shopeepay.voucher-claim.net/redeem<br><br>
                            Login with your ShopeePay account<br>
                            Then the ₱55,000 will be secured
                        </div>
                    </div>
                    <div style="clear: both; margin-top: 20px;">
                        <button class="submit-btn" onclick="showAction(4)">📝 Respond</button>
                    </div>
                </div>
            `,
            question: "Seller sends a 'voucher link' to 'confirm payment'. What is this?",
            actions: [
                { text: "Phishing link to steal credentials!", safe: true },
                { text: "Looks like ShopeePay, click it", safe: false },
                { text: "New payment method, try it", safe: false },
                { text: "Ask for different payment option", safe: false }
            ],
            feedback: {
                correct: "✅ PERFECT! This is a phishing link disguised as ShopeePay. The domain 'shopeepay.voucher-claim.net' is FAKE!",
                incorrect: "❌ PHISHED! You just gave scammers access to your ShopeePay account. They'll drain all your funds immediately!"
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