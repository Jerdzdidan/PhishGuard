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

    .email-header {
        background: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #e0e0e0;
    }

    .email-from {
        font-weight: 600;
        margin-bottom: 5px;
    }

    .email-subject {
        color: #666;
        font-size: 14px;
    }

    .email-body {
        padding: 20px;
        font-size: 14px;
        line-height: 1.6;
    }

    .urgent-badge {
        display: inline-block;
        background: #dc3545;
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 10px;
    }

    .email-button {
        display: inline-block;
        background: #0066cc;
        color: white;
        padding: 12px 24px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        margin-top: 15px;
        cursor: pointer;
    }

    .notification-popup {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        margin: 20px;
    }

    .popup-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
    }

    .popup-icon {
        font-size: 40px;
    }

    .popup-title {
        font-weight: 600;
        font-size: 16px;
    }

    .popup-message {
        font-size: 14px;
        line-height: 1.5;
        color: #666;
    }

    .popup-button {
        width: 100%;
        padding: 12px;
        background: #0066cc;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        margin-top: 15px;
        cursor: pointer;
    }

    .call-screen {
        background: linear-gradient(135deg, #667eea, #764ba2);
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

    .promo-page {
        padding: 30px 20px;
        text-align: center;
    }

    .promo-timer {
        background: #dc3545;
        color: white;
        padding: 15px;
        border-radius: 10px;
        font-size: 24px;
        font-weight: 600;
        margin: 20px 0;
    }

    .promo-prize {
        font-size: 36px;
        color: #ffc107;
        font-weight: bold;
        margin: 20px 0;
    }

    .promo-details {
        background: #fff3cd;
        padding: 15px;
        border-radius: 8px;
        margin: 20px 0;
        font-size: 14px;
    }

    .claim-button {
        width: 100%;
        padding: 15px;
        background: #28c76f;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 20px;
    }

    .survey-page {
        padding: 30px 20px;
    }

    .survey-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .survey-logo {
        font-size: 48px;
        margin-bottom: 10px;
    }

    .survey-question {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .survey-options {
        display: grid;
        gap: 12px;
    }

    .survey-option {
        padding: 15px;
        background: white;
        border: 2px solid #ddd;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .survey-option:hover {
        border-color: #0066cc;
        background: #f0f7ff;
    }

    .reward-banner {
        background: linear-gradient(135deg, #ffc107, #ff9800);
        color: white;
        padding: 15px;
        text-align: center;
        font-weight: 600;
        margin-bottom: 20px;
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
                            <i class="ri-shield-user-line" style="font-size: 4rem; color: #696cff;"></i>
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

                        <h5 class="mb-4">Test your ability to recognize social engineering tactics!</h5>
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
                                <span>10:15 AM</span>
                                <span>📶 5G 🔋 82%</span>
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
                    <strong>Key Concepts:</strong>
                    <ul class="mb-0 mt-2" style="padding-left: 20px;">
                        <li>Authority manipulation</li>
                        <li>Urgency pressure</li>
                        <li>Fear tactics</li>
                        <li>Scarcity tricks</li>
                        <li>Reciprocity exploitation</li>
                    </ul>
                </div>
                <div class="alert alert-warning mb-0">
                    <i class="ri-lightbulb-line me-2"></i>
                    <small><strong>Remember:</strong> Pause → Verify → Report</small>
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

    // 5 Scenarios focusing on social engineering tactics
    const scenarios = [
        {
            name: "Authority - Fake IT Support Email",
            type: "email",
            render: () => `
                <div class="email-header">
                    <div class="email-from">IT Support &lt;support@company-it.com&gt;</div>
                    <div class="email-subject">
                        ACTION REQUIRED: System Maintenance
                        <span class="urgent-badge">URGENT</span>
                    </div>
                </div>
                <div class="email-body">
                    <p><strong>Dear Employee,</strong></p>
                    <p>Our IT Security Team has detected unusual activity on your account. We are performing mandatory system maintenance and need to verify all employee accounts.</p>
                    <p><strong>What you need to do:</strong></p>
                    <ul>
                        <li>Click the button below to verify your account</li>
                        <li>Enter your login credentials</li>
                        <li>Complete the process within 2 hours</li>
                    </ul>
                    <p style="color: #dc3545;"><strong>⚠️ Failure to comply will result in account suspension.</strong></p>
                    <div style="text-align: center;">
                        <button class="email-button" onclick="handleScenarioClick(0)">
                            VERIFY ACCOUNT NOW
                        </button>
                    </div>
                    <p style="margin-top: 20px; font-size: 12px; color: #666;">
                        - IT Security Department<br>
                        Do not reply to this email
                    </p>
                </div>
            `,
            question: "You received this email from 'IT Support'. What should you do?",
            actions: [
                { text: "Click the button to verify before the deadline", safe: false },
                { text: "Pause and contact IT through official channels first", safe: true },
                { text: "Reply to the email asking if it's legitimate", safe: false },
                { text: "Forward to colleagues to check if they got it too", safe: false }
            ],
            feedback: {
                correct: "✅ CORRECT! You recognized authority manipulation. Real IT never asks for credentials via email. Always verify through official channels like your IT helpdesk or internal directory.",
                incorrect: "❌ CAUGHT! This uses authority manipulation - impersonating IT support to pressure you. The urgency and threat of suspension are classic social engineering tactics. Always verify with IT directly."
            }
        },
        {
            name: "Urgency - Limited Time Security Update",
            type: "notification",
            render: () => `
                <div class="notification-popup">
                    <div class="popup-header">
                        <div class="popup-icon">🔐</div>
                        <div>
                            <div class="popup-title">CRITICAL SECURITY UPDATE</div>
                            <div style="font-size: 12px; color: #dc3545;">Expires in 15 minutes</div>
                        </div>
                    </div>
                    <div class="popup-message">
                        <p><strong>Your device is at risk!</strong></p>
                        <p>A critical security vulnerability has been detected. You must install this update immediately to protect your data.</p>
                        <p style="background: #fff3cd; padding: 10px; border-radius: 6px; margin: 15px 0;">
                            <strong>⏰ TIME SENSITIVE:</strong><br>
                            This security patch expires in 15 minutes. After that, your device will be vulnerable to attacks.
                        </p>
                        <p style="font-size: 13px; color: #666;">
                            Update size: 2.3 MB<br>
                            Source: Security Update Center
                        </p>
                    </div>
                    <button class="popup-button" onclick="handleScenarioClick(1)">
                        INSTALL UPDATE NOW
                    </button>
                </div>
            `,
            question: "This popup appeared suddenly. How do you respond?",
            actions: [
                { text: "Install immediately - security is important!", safe: false },
                { text: "Click to see more details about the update", safe: false },
                { text: "Wait 10 minutes then decide", safe: false },
                { text: "Close the popup and check official update channels", safe: true },
            ],
            feedback: {
                correct: "✅ SMART! You didn't fall for urgency manipulation. Legitimate updates don't create artificial time pressure or threaten you. Always check official system settings or app stores for real updates.",
                incorrect: "❌ TRICKED! This is urgency manipulation - creating false time pressure (15-minute deadline) to bypass your logical thinking. Real security updates don't expire or pressure you."
            }
        },
        {
            name: "Fear - Account Suspension Threat",
            type: "call",
            render: () => `
                <div class="call-screen">
                    <div class="caller-avatar">📞</div>
                    <div class="caller-name">Security Department</div>
                    <div class="caller-number">+63 2 8888 9999</div>
                    <div class="call-status">Incoming call...</div>
                    <div style="background: rgba(255,255,255,0.2); padding: 20px; border-radius: 12px; margin: 20px 0; text-align: center;">
                        <p style="margin: 0; font-size: 14px;">
                            <strong>Caller ID shows:</strong><br>
                            "Account Security Alert"
                        </p>
                    </div>
                    <div style="background: rgba(220, 53, 69, 0.3); padding: 15px; border-radius: 10px; margin: 20px 0;">
                        <p style="margin: 0; font-size: 13px;">
                            <strong>📱 Voice Message Preview:</strong><br>
                            "Your account has been flagged for suspicious activity. Press 1 to speak with a security agent or your account will be permanently suspended in 24 hours."
                        </p>
                    </div>
                    <div class="call-actions" onclick="handleScenarioClick(2)">
                        <button class="call-btn answer">📱</button>
                        <button class="call-btn decline">❌</button>
                    </div>
                </div>
            `,
            question: "You see this incoming call with the message. What's your response?",
            actions: [
                { text: "Answer and press 1 to prevent suspension", safe: false },
                { text: "Answer to find out what's wrong", safe: false },
                { text: "Let it ring and call back the number", safe: false },
                { text: "Decline and verify through official support channels", safe: true },
            ],
            feedback: {
                correct: "✅ EXCELLENT! You recognized fear manipulation. The threat of 'permanent suspension' and '24 hours' deadline creates panic. Legitimate companies don't threaten customers via unsolicited calls. Always initiate contact yourself using official numbers.",
                incorrect: "❌ MANIPULATED! This is fear-based social engineering - threatening account suspension to scare you into acting. The 24-hour deadline and 'press 1' demand are classic manipulation tactics. Never respond to unsolicited security calls."
            }
        },
        {
            name: "Scarcity - Limited Reward Offer",
            type: "promo",
            render: () => `
                <div class="promo-page">
                    <div class="survey-logo">🎁</div>
                    <h2 style="color: #0066cc; margin-bottom: 10px;">Congratulations!</h2>
                    <p style="font-size: 16px; color: #666;">You've been selected as one of our valued customers!</p>
                    
                    <div class="promo-timer">
                        ⏰ OFFER EXPIRES IN:<br>
                        <span style="font-size: 36px;">04:37</span>
                    </div>
                    
                    <div class="promo-prize">
                        🏆 Win ₱50,000 Cash!
                    </div>
                    
                    <div class="promo-details">
                        <p style="margin: 0; font-weight: 600;">🎯 EXCLUSIVE OFFER - LIMITED SLOTS</p>
                        <p style="margin: 10px 0 0 0;">Only <strong style="color: #dc3545;">3 slots remaining</strong> out of 5 winners today!</p>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: left;">
                        <p style="margin: 0 0 10px 0; font-weight: 600;">To claim your prize:</p>
                        <ol style="margin: 0; padding-left: 20px; font-size: 14px;">
                            <li>Click "Claim Now" below</li>
                            <li>Verify your identity (ID required)</li>
                            <li>Pay ₱500 processing fee</li>
                            <li>Receive ₱50,000 within 24 hours!</li>
                        </ol>
                    </div>
                    
                    <button class="claim-button" onclick="handleScenarioClick(3)">
                        🎁 CLAIM NOW - HURRY!
                    </button>
                    
                    <p style="font-size: 11px; color: #999; margin-top: 15px;">
                        *Terms and conditions apply. Limited time offer.
                    </p>
                </div>
            `,
            question: "You opened a link and saw this offer. What do you do?",
            actions: [
                { text: "Close immediately - this is a scam using scarcity tactics", safe: true },
                { text: "Claim quickly before slots run out!", safe: false },
                { text: "Share with family to see if they want to claim", safe: false },
                { text: "Pay the ₱500 fee to get the ₱50,000", safe: false }
            ],
            feedback: {
                correct: "✅ WISE! You spotted scarcity manipulation - 'limited slots', countdown timer, and 'only 3 remaining' create false urgency. Legitimate prizes never require payment to claim. The ₱500 'processing fee' is the real scam.",
                incorrect: "❌ SCAMMED! This uses scarcity manipulation - artificial limits ('3 slots left'), countdown timer, and urgency to bypass rational thinking. No real prize requires you to pay money first. The ₱500 fee is stolen money."
            }
        },
        {
            name: "Reciprocity - Survey with Reward",
            type: "survey",
            render: () => `
                <div class="survey-page">
                    <div class="reward-banner">
                        🎁 EARN ₱500 GCASH - 2 MINUTE SURVEY
                    </div>
                    
                    <div class="survey-header">
                        <div class="survey-logo">📊</div>
                        <h3 style="margin: 10px 0;">Customer Satisfaction Survey</h3>
                        <p style="color: #666; margin: 0;">Help us improve and get rewarded!</p>
                    </div>
                    
                    <div class="survey-question">
                        <p style="font-weight: 600; margin-bottom: 15px;">Question 1 of 5:</p>
                        <p style="margin-bottom: 15px;">How satisfied are you with our service?</p>
                        <div class="survey-options">
                            <div class="survey-option">⭐⭐⭐⭐⭐ Very Satisfied</div>
                            <div class="survey-option">⭐⭐⭐⭐ Satisfied</div>
                            <div class="survey-option">⭐⭐⭐ Neutral</div>
                        </div>
                    </div>
                    
                    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <p style="margin: 0; font-weight: 600;">🎁 YOUR REWARD:</p>
                        <p style="margin: 10px 0 0 0; font-size: 14px;">
                            Complete this short survey and receive ₱500 GCash instantly!<br>
                            <strong>We just need your:</strong>
                        </p>
                        <ul style="margin: 10px 0 0 0; font-size: 14px;">
                            <li>Full Name</li>
                            <li>GCash Mobile Number</li>
                            <li>Email Address</li>
                            <li>Valid ID (for verification)</li>
                        </ul>
                    </div>
                    
                    <button class="claim-button" onclick="handleScenarioClick(4)">
                        START SURVEY & GET ₱500
                    </button>
                    
                    <p style="font-size: 12px; color: #666; text-align: center; margin-top: 15px;">
                        ✓ 1,847 people earned ₱500 today!<br>
                        ✓ Takes only 2 minutes
                    </p>
                </div>
            `,
            question: "You received a link to this survey offering ₱500. What's your action?",
            actions: [
                { text: "Complete the survey - easy ₱500!", safe: false },
                { text: "Start but skip personal information questions", safe: false },
                { text: "Ignore it - using reciprocity to collect personal info", safe: true },
                { text: "Share the link so others can earn too", safe: false }
            ],
            feedback: {
                correct: "✅ PROTECTED! You recognized reciprocity manipulation - offering ₱500 to get you to provide valuable personal information including your ID. No legitimate survey requires valid ID, and the 'reward' is just bait to collect your data for fraud or identity theft.",
                incorrect: "❌ EXPLOITED! This uses reciprocity manipulation - offering a reward (₱500) to make you feel obligated to 'help' by sharing personal information. The real goal is harvesting your data for identity theft.The ₱500 will never arrive, but your information will be sold or misused."
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