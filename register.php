<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Login</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/icon-linkup2.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Inter", sans-serif;
            background: linear-gradient(135deg, #f0f7ff 0%, #e6f0ff 50%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            line-height: 1.6;
            position: relative;
            overflow: hidden;
        }

        /* Professional Background Elements */
        .professional-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .floating-icon {
            position: absolute;
            width: 24px;
            height: 24px;
            opacity: 0.4;
            animation: floatIcon 8s ease-in-out infinite;
        }

        .icon-1 {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .icon-2 {
            top: 60%;
            left: 85%;
            animation-delay: 2s;
        }

        .icon-3 {
            top: 80%;
            left: 15%;
            animation-delay: 4s;
        }

        .icon-4 {
            top: 30%;
            left: 75%;
            animation-delay: 6s;
        }

        @keyframes floatIcon {

            0%,
            100% {
                transform: translateY(0);
                opacity: 0.4;
            }

            50% {
                transform: translateY(-20px);
                opacity: 0.7;
            }
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }

        .career-card {
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 440px;
            position: relative;
            overflow: hidden;
            z-index: 5;
            margin: 10px;
        }

        .professional-border {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #3d8eff, #5da1ff, #7db4ff, #5da1ff, #3d8eff);
            background-size: 200% 100%;
            animation: professionalFlow 4s ease-in-out infinite;
            border-radius: 32px 32px 0 0;
        }

        @keyframes professionalFlow {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .career-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .career-logo {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3d8eff;
        }

        .career-logo svg {
            position: relative;
            z-index: 2;
            animation: careerBreath 4s ease-in-out infinite;
        }

        @keyframes careerBreath {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .career-glow {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(61, 142, 255, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            animation: careerGlow 3s ease-in-out infinite;
        }

        @keyframes careerGlow {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.6;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }
        }

        .career-header h1 {
            color: #3d8eff;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .career-header p {
            color: #5da1ff;
            font-size: 15px;
            font-weight: 400;
        }

        /* Professional Form Fields */
        .professional-field {
            position: relative;
            margin-bottom: 32px;
        }

        .field-professional {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(61, 142, 255, 0.05);
            border: 1.5px solid rgba(61, 142, 255, 0.2);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .professional-field input {
            width: 100%;
            background: transparent;
            border: none;
            padding: 18px 60px 18px 20px;
            color: #3d8eff;
            font-size: 16px;
            font-weight: 400;
            outline: none;
            position: relative;
            z-index: 2;
            font-family: inherit;
        }

        .professional-field input::placeholder {
            color: transparent;
        }

        .professional-field label {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #7db4ff;
            font-size: 16px;
            font-weight: 400;
            pointer-events: none;
            transition: all 0.3s ease;
            z-index: 3;
            background: rgba(255, 255, 255, 0.95);
            padding: 0 8px;
        }

        .professional-field input:focus+label,
        .professional-field input:not(:placeholder-shown)+label {
            top: 0;
            font-size: 13px;
            font-weight: 500;
            color: #3d8eff;
            transform: translateY(-50%);
        }

        .professional-field input:focus~.field-professional {
            border-color: #3d8eff;
            background: rgba(61, 142, 255, 0.1);
            box-shadow: 0 0 0 2px rgba(61, 142, 255, 0.2);
        }

        .growth-indicator {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            z-index: 4;
        }

        .career-sprout {
            width: 100%;
            height: 100%;
            background: #3d8eff;
            border-radius: 50%;
            opacity: 0;
            transform: scale(0);
            animation: sproutGrow 0.5s ease-out forwards;
        }

        .professional-field input:focus~.growth-indicator .career-sprout,
        .professional-field input:valid~.growth-indicator .career-sprout {
            opacity: 1;
            transform: scale(1);
        }

        @keyframes sproutGrow {
            0% {
                opacity: 0;
                transform: scale(0);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Professional Toggle */
        .professional-field:has(.professional-toggle) input {
            padding-right: 48px;
        }

        .professional-toggle {
            position: absolute;
            right: 12px;
            top: 16px;
            background: none;
            border: none;
            cursor: pointer;
            color: #7db4ff;
            padding: 4px;
            border-radius: 8px;
            transition: all 0.3s ease;
            z-index: 4;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
        }

        .professional-toggle:hover {
            color: #3d8eff;
            background: rgba(61, 142, 255, 0.1);
        }

        .eye-hidden {
            display: none;
        }

        .professional-toggle.toggle-visible .eye-visible {
            display: none;
        }

        .professional-toggle.toggle-visible .eye-hidden {
            display: block;
        }

        /* Career Options */
        .career-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 36px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .career-checkbox {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 14px;
            color: #3d8eff;
            font-weight: 500;
        }

        .career-checkbox input[type="checkbox"] {
            display: none;
        }

        .checkbox-career {
            width: 22px;
            height: 22px;
            margin-right: 12px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .career-shape {
            width: 100%;
            height: 100%;
            background: rgba(61, 142, 255, 0.1);
            border: 1.5px solid rgba(61, 142, 255, 0.3);
            border-radius: 4px;
            transition: all 0.3s ease;
            position: absolute;
        }

        .checkbox-career svg {
            color: transparent;
            transition: color 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .career-checkbox input[type="checkbox"]:checked+.checkbox-career .career-shape {
            background: #3d8eff;
            border-color: #3d8eff;
            box-shadow: 0 0 12px rgba(61, 142, 255, 0.4);
        }

        .career-checkbox input[type="checkbox"]:checked+.checkbox-career svg {
            color: #ffffff;
        }

        .career-link {
            color: #3d8eff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .career-link::after {
            content: "";
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #3d8eff, #5da1ff);
            transition: width 0.3s ease;
        }

        .career-link:hover::after {
            width: 100%;
        }

        .career-link:hover {
            color: #2a6fd1;
        }

        /* Career Button */
        .career-button {
            width: 100%;
            background: transparent;
            color: #ffffff;
            border: none;
            border-radius: 20px;
            padding: 0;
            cursor: pointer;
            font-family: inherit;
            font-size: 16px;
            font-weight: 600;
            position: relative;
            margin-bottom: 32px;
            overflow: hidden;
            min-height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .button-career {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #3d8eff, #5da1ff, #7db4ff);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .career-button:hover .button-career {
            background: linear-gradient(135deg, #2a6fd1, #3d8eff, #5da1ff);
            transform: scale(1.02);
        }

        .career-button:active .button-career {
            transform: scale(0.98);
        }

        .button-text {
            position: relative;
            z-index: 2;
            transition: opacity 0.3s ease;
        }

        .button-growth {
            position: absolute;
            z-index: 2;
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            gap: 6px;
        }

        .growing-circle {
            width: 8px;
            height: 8px;
            background: #ffffff;
            border-radius: 50%;
            animation: professionalGrow 1.5s ease-in-out infinite;
        }

        .circle-2 {
            animation-delay: 0.2s;
        }

        .circle-3 {
            animation-delay: 0.4s;
        }

        @keyframes professionalGrow {

            0%,
            80%,
            100% {
                transform: scale(0.8);
                opacity: 0.6;
            }

            40% {
                transform: scale(1.2);
                opacity: 1;
            }
        }

        .career-button.loading .button-text {
            opacity: 0;
        }

        .career-button.loading .button-growth {
            opacity: 1;
        }

        .button-aura {
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            background: linear-gradient(135deg, #3d8eff, #7db4ff);
            border-radius: 23px;
            opacity: 0;
            filter: blur(12px);
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .career-button:hover .button-aura {
            opacity: 0.6;
        }

        /* Career Divider */
        .career-divider {
            display: flex;
            align-items: center;
            margin: 32px 0;
            gap: 16px;
        }

        .divider-branch {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(61, 142, 255, 0.4), transparent);
        }

        .divider-center {
            color: #3d8eff;
            opacity: 0.7;
            animation: centerPulse 3s ease-in-out infinite;
        }

        @keyframes centerPulse {

            0%,
            100% {
                opacity: 0.7;
                transform: scale(1);
            }

            50% {
                opacity: 1;
                transform: scale(1.1);
            }
        }

        /* Professional Social */
        .professional-social {
            display: flex;
            gap: 12px;
            margin-bottom: 32px;
        }

        .career-social {
            flex: 1;
            background: transparent;
            color: #3d8eff;
            border: none;
            border-radius: 16px;
            padding: 14px 16px;
            cursor: pointer;
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            min-height: 48px;
            position: relative;
            overflow: hidden;
        }

        .social-career {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(61, 142, 255, 0.1);
            border: 1.5px solid rgba(61, 142, 255, 0.2);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .career-social:hover .social-career {
            background: rgba(61, 142, 255, 0.15);
            border-color: rgba(61, 142, 255, 0.4);
        }

        .career-social span,
        .career-social svg {
            position: relative;
            z-index: 2;
        }

        .social-glow {
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: rgba(61, 142, 255, 0.3);
            border-radius: 18px;
            opacity: 0;
            filter: blur(8px);
            transition: opacity 0.3s ease;
            z-index: 0;
        }

        .career-social:hover .social-glow {
            opacity: 1;
        }

        /* Career Signup */
        .career-signup {
            text-align: center;
            font-size: 14px;
            color: #5da1ff;
        }

        .growth-link {
            color: #3d8eff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .growth-link::after {
            content: "";
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #3d8eff, #5da1ff);
            transition: width 0.3s ease;
        }

        .growth-link:hover::after {
            width: 100%;
        }

        .growth-link:hover {
            color: #2a6fd1;
        }

        /* Gentle Error */
        .gentle-error {
            color: #ff7043;
            font-size: 12px;
            font-weight: 500;
            position: absolute;
            bottom: -20px;
            left: 5px;
            right: 0;
            padding: 0 5px;
            opacity: 0;
            transform: translateY(5px);
            transition: all 0.3s ease;
            z-index: 5;
            text-align: left;
            white-space: nowrap;
            overflow: visible;
        }

        .gentle-error.show {
            opacity: 1;
            transform: translateY(0);
        }

        .professional-field.error .field-professional {
            border-color: #ff7043;
            background: rgba(255, 112, 67, 0.1);
        }

        .professional-field.error label {
            color: #ff5722;
        }

        /* Career Success */
        .career-success {
            display: none;
            text-align: center;
            padding: 40px 20px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
        }

        .career-success.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .success-mandala {
            position: relative;
            width: 90px;
            height: 90px;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mandala-ring {
            position: absolute;
            border: 2px solid #3d8eff;
            border-radius: 50%;
            animation: mandalaGrow 1.2s ease-out forwards;
            opacity: 0;
        }

        .ring-1 {
            width: 60px;
            height: 60px;
            top: 15px;
            left: 15px;
            animation-delay: 0s;
        }

        .ring-2 {
            width: 75px;
            height: 75px;
            top: 7.5px;
            left: 7.5px;
            animation-delay: 0.2s;
        }

        .ring-3 {
            width: 90px;
            height: 90px;
            top: 0;
            left: 0;
            animation-delay: 0.4s;
        }

        @keyframes mandalaGrow {
            0% {
                opacity: 0;
                transform: scale(0) rotate(0deg);
            }

            50% {
                opacity: 1;
                transform: scale(1.1) rotate(180deg);
            }

            100% {
                opacity: 0.8;
                transform: scale(1) rotate(360deg);
            }
        }

        .mandala-center {
            position: relative;
            z-index: 2;
            color: #3d8eff;
            animation: centerBloom 0.8s ease-out 0.6s forwards;
            opacity: 0;
        }

        @keyframes centerBloom {
            0% {
                opacity: 0;
                transform: scale(0);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .career-success h3 {
            color: #3d8eff;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .career-success p {
            color: #5da1ff;
            font-size: 14px;
        }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            body {
                padding: 16px;
            }

            .career-card {
                padding: 36px 28px;
                border-radius: 24px;
            }

            .career-header h1 {
                font-size: 1.75rem;
            }

            .career-logo {
                width: 60px;
                height: 60px;
            }

            .career-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .professional-social {
                flex-direction: column;
            }

            .floating-icon {
                width: 20px;
                height: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="professional-background">
        <div class="floating-icon icon-1">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="#3d8eff" stroke-width="1.5" />
                <path d="M16.5 16.5L21 21" stroke="#3d8eff" stroke-width="1.5" stroke-linecap="round" />
            </svg>
        </div>
        <div class="floating-icon icon-2">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="#3d8eff" stroke-width="1.5" />
                <path d="M16.5 16.5L21 21" stroke="#3d8eff" stroke-width="1.5" stroke-linecap="round" />
            </svg>
        </div>
        <div class="floating-icon icon-3">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="#3d8eff" stroke-width="1.5" />
                <path d="M16.5 16.5L21 21" stroke="#3d8eff" stroke-width="1.5" stroke-linecap="round" />
            </svg>
        </div>
        <div class="floating-icon icon-4">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="8" stroke="#3d8eff" stroke-width="1.5" />
                <path d="M16.5 16.5L21 21" stroke="#3d8eff" stroke-width="1.5" stroke-linecap="round" />
            </svg>
        </div>
    </div>

    <div class="login-container">
        <div class="career-card">
            <div class="professional-border"></div>

            <div class="career-header">
                <div class="career-logo">
                    <img src="assets/img/icon-linkup2.png" alt="LinkUp Logo"
                        style="width: 100%; height: auto; max-width: 300px;">
                    <div class="career-glow"></div>
                </div>
                <h1>LinkUp</h1>
                <p>Baru di LinkUp? Bergabunglah dengan ribuan profesional!</p>
            </div>

            <form class="harmony-form" id="loginForm" novalidate>
                <div class="professional-field">
                    <div class="field-professional"></div>
                    <input type="text" id="fullname" name="fullname" required autocomplete="name">
                    <label for="fullname">Nama Lengkap</label>
                    <div class="growth-indicator">
                        <div class="career-sprout"></div>
                    </div>
                    <span class="gentle-error" id="fullnameError"></span>
                </div>

                <div class="professional-field">
                    <div class="field-professional"></div>
                    <input type="email" id="email" name="email" required autocomplete="email">
                    <label for="email">Email</label>
                    <div class="growth-indicator">
                        <div class="career-sprout"></div>
                    </div>
                    <span class="gentle-error" id="emailError"></span>
                </div>

                <div class="professional-field">
                    <div class="field-professional"></div>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                    <label for="password">Kata Sandi</label>
                    <button type="button" class="professional-toggle" id="passwordToggle"
                        aria-label="Toggle password visibility">
                        <svg class="eye-visible" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path
                                d="M10 4c-4 0-7 3-8 6 1 3 4 6 8 6s7-3 8-6c-1-3-4-6-8-6zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z"
                                fill="currentColor" />
                        </svg>
                        <svg class="eye-hidden" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path
                                d="M3 3l14 14M8.5 8.5a2 2 0 002.83 2.83m-.83-4.83a4 4 0 014 4M10 6C6 6 3 9 2 12c.5 1.5 2 3.5 4 4.5M10 14c4 0 7-3 8-6-.5-1.5-2-3.5-4-4.5"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <span class="gentle-error" id="passwordError"></span>
                </div>

                <button type="submit" class="career-button">
                    <div class="button-career"></div>
                    <span class="button-text">Sign Up</span>
                    <div class="button-growth">
                        <div class="growing-circle circle-1"></div>
                        <div class="growing-circle circle-2"></div>
                        <div class="growing-circle circle-3"></div>
                    </div>
                    <div class="button-aura"></div>
                </button>
            </form>

            <div class="career-signup">
                <span>Sudah punya akun?</span>
                <a href="login.php" class="growth-link">Masuk</a>
            </div>

            <div class="career-success" id="successMessage">
                <div class="success-mandala">
                    <div class="mandala-ring ring-1"></div>
                    <div class="mandala-ring ring-2"></div>
                    <div class="mandala-ring ring-3"></div>
                    <div class="mandala-center">
                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                            <path d="M8 14l6 6 12-12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <h3>Welcome to JobFinder</h3>
                <p>Your dream job awaits...</p>
            </div>
        </div>
    </div>

    <script>
        // Job Portal Login Form JavaScript
        class JobFinderLoginForm {
            constructor() {
                this.form = document.getElementById('loginForm');
                this.fullnameInput = document.getElementById('fullname');
                this.emailInput = document.getElementById('email');
                this.passwordInput = document.getElementById('password');
                this.passwordToggle = document.getElementById('passwordToggle');
                this.submitButton = this.form.querySelector('.career-button');
                this.successMessage = document.getElementById('successMessage');
                this.socialButtons = document.querySelectorAll('.career-social');

                this.init();
            }

            init() {
                this.bindEvents();
                this.setupPasswordToggle();
                this.setupSocialButtons();
                this.setupCareerEffects();
            }

            bindEvents() {
                this.form.addEventListener('submit', (e) => this.handleSubmit(e));
                this.fullnameInput.addEventListener('blur', () => this.validateFullname());
                this.emailInput.addEventListener('blur', () => this.validateEmail());
                this.passwordInput.addEventListener('blur', () => this.validatePassword());
                this.fullnameInput.addEventListener('input', () => this.clearError('fullname'));
                this.emailInput.addEventListener('input', () => this.clearError('email'));
                this.passwordInput.addEventListener('input', () => this.clearError('password'));

                // Add placeholder for label animations
                this.fullnameInput.setAttribute('placeholder', ' ');
                this.emailInput.setAttribute('placeholder', ' ');
                this.passwordInput.setAttribute('placeholder', ' ');
            }

            setupPasswordToggle() {
                this.passwordToggle.addEventListener('click', () => {
                    const type = this.passwordInput.type === 'password' ? 'text' : 'password';
                    this.passwordInput.type = type;

                    this.passwordToggle.classList.toggle('toggle-visible', type === 'text');
                });
            }

            setupSocialButtons() {
                this.socialButtons.forEach(button => {
                    button.addEventListener('click', (e) => {
                        const provider = button.querySelector('span').textContent.trim();
                        this.handleSocialLogin(provider, button);
                    });
                });
            }

            setupCareerEffects() {
                // Add professional focus effects
                [this.fullnameInput, this.emailInput, this.passwordInput].forEach(input => {
                    input.addEventListener('focus', (e) => {
                        this.triggerCareerEffect(e.target.closest('.professional-field'));
                    });

                    input.addEventListener('blur', (e) => {
                        this.resetCareerEffect(e.target.closest('.professional-field'));
                    });
                });
            }

            triggerCareerEffect(field) {
                // Add gentle breathing effect to the field
                const fieldProfessional = field.querySelector('.field-professional');
                fieldProfessional.style.animation = 'gentleBreath 3s ease-in-out infinite';
            }

            resetCareerEffect(field) {
                // Remove breathing effect
                const fieldProfessional = field.querySelector('.field-professional');
                fieldProfessional.style.animation = '';
            }

            validateFullname() {
                const fullname = this.fullnameInput.value.trim();
                const nameRegex = /^[a-zA-Z\s]{3,}$/;

                if (!fullname) {
                    this.showError('fullname', 'Mohon masukkan nama lengkap');
                    return false;
                }

                if (fullname.length < 3) {
                    this.showError('fullname', 'Nama minimal 3 karakter');
                    return false;
                }

                if (!nameRegex.test(fullname)) {
                    this.showError('fullname', 'Nama hanya boleh berisi huruf dan spasi');
                    return false;
                }

                this.clearError('fullname');
                return true;
            }

            validateEmail() {
                const email = this.emailInput.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!email) {
                    this.showError('email', 'Mohon masukkan alamat email');
                    return false;
                }

                if (!emailRegex.test(email)) {
                    this.showError('email', 'Format email tidak valid');
                    return false;
                }

                this.clearError('email');
                return true;
            }

            validatePassword() {
                const password = this.passwordInput.value;

                if (!password) {
                    this.showError('password', 'Mohon masukkan kata sandi');
                    return false;
                }

                if (password.length < 6) {
                    this.showError('password', 'Kata sandi minimal 6 karakter');
                    return false;
                }

                this.clearError('password');
                return true;
            }

            showError(field, message) {
                const professionalField = document.getElementById(field).closest('.professional-field');
                const errorElement = document.getElementById(`${field}Error`);

                professionalField.classList.add('error');
                errorElement.textContent = message;
                errorElement.classList.add('show');
            }

            clearError(field) {
                const professionalField = document.getElementById(field).closest('.professional-field');
                const errorElement = document.getElementById(`${field}Error`);

                professionalField.classList.remove('error');
                errorElement.classList.remove('show');
                setTimeout(() => {
                    errorElement.textContent = '';
                }, 300);
            }

            async handleSubmit(e) {
                e.preventDefault();

                const isFullnameValid = this.validateFullname();
                const isEmailValid = this.validateEmail();
                const isPasswordValid = this.validatePassword();

                if (!isFullnameValid || !isEmailValid || !isPasswordValid) {
                    return;
                }

                this.setLoading(true);

                try {
                    // Simulate authentication process
                    await new Promise(resolve => setTimeout(resolve, 2000));

                    // Show success
                    this.showCareerSuccess();
                } catch (error) {
                    this.showError('password', 'Login failed. Please try again.');
                } finally {
                    this.setLoading(false);
                }
            }

            async handleSocialLogin(provider, button) {
                console.log(`Connecting with ${provider}...`);

                // Loading state
                const originalHTML = button.innerHTML;
                button.style.pointerEvents = 'none';
                button.style.opacity = '0.7';

                const loadingHTML = `
                    <div class="social-career"></div>
                    <div style="display: flex; gap: 4px;">
                        <div style="width: 6px; height: 6px; background: #3d8eff; border-radius: 50%; animation: professionalGrow 1.5s ease-in-out infinite;"></div>
                        <div style="width: 6px; height: 6px; background: #3d8eff; border-radius: 50%; animation: professionalGrow 1.5s ease-in-out infinite; animation-delay: 0.2s;"></div>
                        <div style="width: 6px; height: 6px; background: #3d8eff; border-radius: 50%; animation: professionalGrow 1.5s ease-in-out infinite; animation-delay: 0.4s;"></div>
                    </div>
                    <span>Connecting...</span>
                    <div class="social-glow"></div>
                `;

                button.innerHTML = loadingHTML;

                try {
                    await new Promise(resolve => setTimeout(resolve, 2000));
                    console.log(`Redirecting to ${provider} authentication...`);
                    // window.location.href = `/auth/${provider.toLowerCase()}`;
                } catch (error) {
                    console.error(`${provider} connection failed: ${error.message}`);
                } finally {
                    button.style.pointerEvents = 'auto';
                    button.style.opacity = '1';
                    button.innerHTML = originalHTML;
                }
            }

            setLoading(loading) {
                this.submitButton.classList.toggle('loading', loading);
                this.submitButton.disabled = loading;

                // Disable social buttons during processing
                this.socialButtons.forEach(button => {
                    button.style.pointerEvents = loading ? 'none' : 'auto';
                    button.style.opacity = loading ? '0.6' : '1';
                });
            }

            showCareerSuccess() {
                // Hide form with transition
                this.form.style.transform = 'scale(0.95)';
                this.form.style.opacity = '0';

                setTimeout(() => {
                    this.form.style.display = 'none';
                    document.querySelector('.professional-social').style.display = 'none';
                    document.querySelector('.career-signup').style.display = 'none';
                    document.querySelector('.career-divider').style.display = 'none';

                    // Show success
                    this.successMessage.classList.add('show');

                }, 300);

                // Redirect after success
                setTimeout(() => {
                    console.log('Welcome to JobFinder...');
                    // window.location.href = '/dashboard';
                }, 3000);
            }
        }

        // Add gentle breathing animation to CSS dynamically
        if (!document.querySelector('#career-keyframes')) {
            const style = document.createElement('style');
            style.id = 'career-keyframes';
            style.textContent = `
                @keyframes gentleBreath {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.01); }
                }
            `;
            document.head.appendChild(style);
        }

        // Initialize the career form when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new JobFinderLoginForm();
        });
    </script>
</body>

</html>