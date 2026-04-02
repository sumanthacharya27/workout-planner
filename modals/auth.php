<?php
/**
 * Auth Backend Bridge
 * This section handles AJAX requests for login, register, and status checks.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_GET['action']) && $_GET['action'] === 'check')) {
    header('Content-Type: application/json');
    try {
        require_once dirname(__DIR__) . '/includes/response.php';
        require_once dirname(__DIR__) . '/includes/auth.php';

        // Verify database connection
        if (!isset($db) || !$db->getConnection()) {
            throw new Exception("Database connection not initialized.");
        }

        $data = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON input.");
            }
        }
        
        $action = $data['action'] ?? $_GET['action'] ?? '';

        if ($action === 'login') {
            echo json_encode($auth->login($data['email'] ?? '', $data['password'] ?? ''));
            exit;
        } elseif ($action === 'register') {
            echo json_encode($auth->register($data['email'] ?? '', $data['password'] ?? '', $data['name'] ?? ''));
            exit;
        } elseif ($action === 'check') {
            echo json_encode(['logged_in' => $auth->isLoggedIn(), 'user_name' => $auth->getCurrentUserName()]);
            exit;
        }
    } catch (Throwable $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Backend Error: ' . $e->getMessage()
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>THE KINETIC EDITORIAL — Auth</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Epilogue:ital,wght@0,700;0,800;0,900;1,700;1,800;1,900&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        brand: { DEFAULT: "#ff5d2e", dim: "#cc3d12", glow: "rgba(255,93,46,0.25)" },
                        amber: { DEFAULT: "#fd8b00", dim: "#cc6e00" },
                        surface: { DEFAULT: "#0a0a0a", card: "#111111", raised: "#181818", border: "#282828" },
                        ink: { DEFAULT: "#ffffff", muted: "#888888", faint: "#444444" },
                    },
                    fontFamily: { headline: ["Epilogue"], body: ["DM Sans"] },
                    animation: {
                        "fade-up": "fadeUp 0.5s ease both",
                        "fade-in": "fadeIn 0.4s ease both",
                        "slide-left": "slideLeft 0.35s cubic-bezier(0.4,0,0.2,1) both",
                        "slide-right": "slideRight 0.35s cubic-bezier(0.4,0,0.2,1) both",
                        "pulse-ring": "pulseRing 1.5s ease infinite",
                        "spin-slow": "spin 1s linear infinite",
                    },
                    keyframes: {
                        fadeUp: { "0%": { opacity: 0, transform: "translateY(18px)" }, "100%": { opacity: 1, transform: "translateY(0)" } },
                        fadeIn: { "0%": { opacity: 0 }, "100%": { opacity: 1 } },
                        slideLeft: { "0%": { opacity: 0, transform: "translateX(24px)" }, "100%": { opacity: 1, transform: "translateX(0)" } },
                        slideRight: { "0%": { opacity: 0, transform: "translateX(-24px)" }, "100%": { opacity: 1, transform: "translateX(0)" } },
                        pulseRing: { "0%,100%": { boxShadow: "0 0 0 0 rgba(255,93,46,0.35)" }, "50%": { boxShadow: "0 0 0 10px rgba(255,93,46,0)" } },
                    },
                    boxShadow: {
                        "card": "0 24px 64px rgba(0,0,0,0.7), 0 1px 0 rgba(255,255,255,0.05) inset",
                        "brand": "0 8px 30px rgba(255,93,46,0.35)",
                        "glow": "0 0 20px rgba(255,93,46,0.2)",
                    }
                },
            }
        };
    </script>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #0a0a0a;
            color: #fff;
            overflow-x: hidden;
        }

        /* ── Grain overlay ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");
            opacity: 0.6;
        }

        /* ── Hero image ── */
        .hero-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
        }

        .hero-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.25) saturate(0.6);
        }

        .hero-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 70% 60% at 50% 80%, rgba(255, 93, 46, 0.08) 0%, transparent 70%),
                linear-gradient(to top, #0a0a0a 0%, transparent 50%),
                linear-gradient(to bottom, #0a0a0a 0%, transparent 30%);
        }

        /* ── Card ── */
        .auth-card {
            background: rgba(14, 14, 14, 0.85);
            backdrop-filter: blur(28px) saturate(160%);
            -webkit-backdrop-filter: blur(28px) saturate(160%);
            border: 1px solid rgba(255, 255, 255, 0.07);
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.7), 0 1px 0 rgba(255, 255, 255, 0.05) inset;
        }

        /* ── Input ── */
        .field {
            position: relative;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .field:focus-within {
            background: rgba(255, 93, 46, 0.04);
            border-color: rgba(255, 93, 46, 0.45);
            box-shadow: 0 0 0 3px rgba(255, 93, 46, 0.1);
        }

        .field.error-field {
            border-color: rgba(255, 80, 80, 0.6);
            box-shadow: 0 0 0 3px rgba(255, 80, 80, 0.1);
        }

        .field input {
            width: 100%;
            padding: 14px 14px 14px 46px;
            background: transparent;
            border: none;
            outline: none;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 400;
        }

        .field input::placeholder {
            color: #555;
        }

        .field .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #555;
            font-size: 19px;
            pointer-events: none;
            transition: color 0.2s;
        }

        .field:focus-within .icon {
            color: #ff5d2e;
        }

        .field .suffix {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #555;
            padding: 4px;
            font-size: 19px;
            transition: color 0.2s;
            line-height: 1;
        }

        .field .suffix:hover {
            color: #aaa;
        }

        /* ── Password strength ── */
        .strength-bar {
            height: 3px;
            border-radius: 2px;
            transition: width 0.4s ease, background 0.4s ease;
        }

        /* ── Tab underline ── */
        .tab-track {
            position: absolute;
            bottom: -1px;
            height: 2px;
            transition: left 0.35s cubic-bezier(0.4, 0, 0.2, 1), width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(90deg, #ff5d2e, #fd8b00);
            border-radius: 2px;
        }

        /* ── CTA button ── */
        .btn-cta {
            width: 100%;
            background: linear-gradient(135deg, #ff5d2e 0%, #fd6422 50%, #fd8b00 100%);
            color: #fff;
            font-family: 'Epilogue', sans-serif;
            font-weight: 900;
            font-style: italic;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            border: none;
            border-radius: 100px;
            padding: 16px 24px;
            font-size: 14px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.15s, box-shadow 0.2s, filter 0.2s;
            box-shadow: 0 8px 30px rgba(255, 93, 46, 0.35);
        }

        .btn-cta:hover {
            filter: brightness(1.1);
            box-shadow: 0 12px 40px rgba(255, 93, 46, 0.5);
            transform: translateY(-1px);
        }

        .btn-cta:active {
            transform: scale(0.97) translateY(0);
        }

        .btn-cta:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-cta .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            transform: scale(0);
            animation: rippleAnim 0.5s linear;
            pointer-events: none;
        }

        @keyframes rippleAnim {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* ── Social button ── */
        .btn-social {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.2s;
            color: #aaa;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
        }

        .btn-social:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        /* ── Toast ── */
        .toast {
            position: fixed;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%) translateY(80px);
            min-width: 280px;
            max-width: 400px;
            padding: 14px 20px;
            border-radius: 14px;
            font-size: 13.5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(20px);
            border: 1px solid;
            z-index: 9999;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
            opacity: 0;
            pointer-events: none;
        }

        .toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        .toast.success {
            background: rgba(20, 40, 20, 0.9);
            border-color: rgba(60, 200, 80, 0.35);
            color: #6dff88;
        }

        .toast.error {
            background: rgba(40, 10, 10, 0.9);
            border-color: rgba(255, 80, 80, 0.35);
            color: #ff8080;
        }

        .toast .toast-icon {
            font-size: 18px;
            flex-shrink: 0;
        }

        /* ── Spinner ── */
        .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Form panel transition ── */
        .form-panel {
            display: none;
        }

        .form-panel.active {
            display: block;
        }

        /* ── Field error message ── */
        .field-error {
            font-size: 11.5px;
            color: #ff7070;
            margin-top: 5px;
            padding-left: 4px;
            display: none;
        }

        .field-error.show {
            display: block;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #282828;
            border-radius: 4px;
        }

        /* ── Responsive ── */
        @media (max-height: 700px) {
            .auth-card {
                max-height: 92vh;
                overflow-y: auto;
            }
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 350, 'GRAD' 0, 'opsz' 22;
        }
    </style>
</head>

<body>

    <!-- ═══ Background ═══════════════════════════════════════════════════════════ -->
    <div class="hero-bg">
        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuB5GSnkeb8rzAbj22z4UI2o3kAS4Tb8eELoRN0Zg4jzJfCZQKmTE8ucYFmnsg_Dk49SrUCGRCMIhEhpaQr7ko3-JeuIcA7TX6zxFyxKc6P4cAp_GBWrGAXMhwhG5-jSOjQBXw9gRn1a5YUJ_enmr5_dgBzQm7kphy5sEdu4CGJ-lzS7CDbUxO70fQgvtMg5LTQOiXcmAqc0ovgsDFpkecFEa0LMJpvPLSIagv-yj2jCnsfZQM8P2EO-_k32sLBpXO9O4UevhAJT5Bqz"
            alt="Gym interior" />
    </div>

    <!-- ═══ Top Bar ════════════════════════════════════════════════════════════== -->
    <header class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-6 sm:px-10 py-5">
        <div
            style="font-family:'Epilogue',sans-serif; font-weight:900; font-style:italic; font-size:17px; letter-spacing:0.22em; color:#ff5d2e;">
            THE KINETIC EDITORIAL
        </div>
        <span class="material-symbols-outlined"
            style="color:#ff5d2e; font-size:22px; font-variation-settings:'FILL' 1,'wght' 400;">fitness_center</span>
    </header>

    <!-- ═══ Main Content ══════════════════════════════════════════════════════════ -->
    <main class="relative z-10 min-h-screen flex items-center justify-center px-4 py-24">
        <div id="authModal" class="auth-card modal hidden w-full max-w-[420px] rounded-2xl p-8 sm:p-10 animate-fade-up">

            <!-- Brand Icon -->
            <div class="flex justify-center mb-7">
                <div
                    style="width:54px;height:54px;border-radius:16px;background:rgba(255,93,46,0.12);border:1px solid rgba(255,93,46,0.2);display:flex;align-items:center;justify-content:center;">
                    <span class="material-symbols-outlined"
                        style="color:#ff5d2e;font-size:28px;font-variation-settings:'FILL' 1,'wght' 400;">local_fire_department</span>
                </div>
            </div>

            <!-- Headline -->
            <div class="text-center mb-8">
                <h1
                    style="font-family:'Epilogue',sans-serif;font-weight:900;font-style:italic;font-size:clamp(26px,5vw,32px);letter-spacing:-0.02em;line-height:1.1;text-transform:uppercase;">
                    Train Hard.<br /><span style="color:#ff5d2e;">Stay Strong.</span>
                </h1>
                <p id="cardSubtitle" style="color:#666;font-size:13.5px;margin-top:8px;">Sign in to your account</p>
            </div>

            <!-- ─── Tab Toggle ──────────────────────────────────── -->
            <div id="tabBar" class="relative flex border-b mb-8" style="border-color:#282828;">
                <button id="tabLogin" onclick="switchTab('login')" data-form="login"
                    class="toggle-btn flex-1 pb-3 text-xs font-bold uppercase tracking-widest transition-colors duration-200 active"
                    style="font-family:'Epilogue',sans-serif;color:#ff5d2e;">Login</button>
                <button id="tabRegister" onclick="switchTab('register')" data-form="register"
                    class="toggle-btn flex-1 pb-3 text-xs font-bold uppercase tracking-widest transition-colors duration-200"
                    style="font-family:'Epilogue',sans-serif;color:#444;">Register</button>
                <div id="tabTrack" class="tab-track" style="left:0;width:50%;"></div>
            </div>

            <!-- ─── Login Form ─────────────────────────────────── -->
            <div id="panelLogin" class="form-panel form-section active animate-slide-right">
                <form id="loginForm" novalidate autocomplete="on">
                    <div class="space-y-3">

                        <div>
                            <div class="field" id="lf-email-wrap">
                                <span class="material-symbols-outlined icon">mail</span>
                                <input id="lf-email" name="email" type="email" placeholder="Email address" autocomplete="email" />
                            </div>
                            <p class="field-error" id="lf-email-err">Please enter a valid email.</p>
                        </div>

                        <div>
                            <div class="field" id="lf-pass-wrap">
                                <span class="material-symbols-outlined icon">lock</span>
                                <input id="lf-pass" name="password" type="password" placeholder="Password"
                                    autocomplete="current-password" style="padding-right:46px;" />
                                <button type="button" class="suffix" onclick="togglePass('lf-pass', this)"
                                    tabindex="-1">
                                    <span class="material-symbols-outlined"
                                        style="font-size:19px;">visibility_off</span>
                                </button>
                            </div>
                            <p class="field-error" id="lf-pass-err">Password is required.</p>
                        </div>

                    </div>

                    <div class="flex justify-end mt-3 mb-6">
                        <a href="#"
                            style="font-size:12px;font-weight:600;color:#fd8b00;text-transform:uppercase;letter-spacing:0.08em;text-decoration:none;transition:color 0.2s;"
                            onmouseover="this.style.color='#ffa733'" onmouseout="this.style.color='#fd8b00'">Forgot
                            password?</a>
                    </div>

                    <button type="submit" class="btn-cta" id="loginBtn" onclick="ripple(event)">
                        <span id="loginBtnContent" class="flex items-center justify-center gap-2">Sign In</span>
                    </button>

                    <p class="text-center mt-5" style="font-size:12.5px;color:#555;">
                        No account?
                        <a href="#" onclick="switchTab('register');return false;"
                            style="color:#ff5d2e;font-weight:600;text-decoration:none;margin-left:2px;">Create one
                            free</a>
                    </p>
                </form>
            </div>

            <!-- ─── Register Form ──────────────────────────────── -->
            <div id="panelRegister" class="form-panel form-section animate-slide-left hidden">
                <form id="registerForm" novalidate autocomplete="on">
                    <div class="space-y-3">

                        <div>
                            <div class="field" id="rf-name-wrap">
                                <span class="material-symbols-outlined icon">person</span>
                                <input id="regName" name="name" type="text" placeholder="Full name" autocomplete="name" />
                            </div>
                            <p class="field-error" id="rf-name-err">Name must be at least 2 characters.</p>
                        </div>

                        <div>
                            <div class="field" id="rf-email-wrap">
                                <span class="material-symbols-outlined icon">mail</span>
                                <input id="regEmail" name="email" type="email" placeholder="Email address" autocomplete="email" />
                            </div>
                            <p class="field-error" id="rf-email-err">Please enter a valid email.</p>
                        </div>

                        <div>
                            <div class="field" id="rf-pass-wrap">
                                <span class="material-symbols-outlined icon">lock</span>
                                <input id="regPass" name="password" type="password" placeholder="Password (min 8 chars)"
                                    autocomplete="new-password" oninput="updateStrength(this.value)"
                                    style="padding-right:46px;" />
                                <button type="button" class="suffix" onclick="togglePass('rf-pass', this)"
                                    tabindex="-1">
                                    <span class="material-symbols-outlined"
                                        style="font-size:19px;">visibility_off</span>
                                </button>
                            </div>
                            <!-- Strength bar -->
                            <div class="flex gap-1 mt-2 px-1" id="strengthBars">
                                <div class="strength-bar flex-1 bg-white/10" id="sb1"></div>
                                <div class="strength-bar flex-1 bg-white/10" id="sb2"></div>
                                <div class="strength-bar flex-1 bg-white/10" id="sb3"></div>
                                <div class="strength-bar flex-1 bg-white/10" id="sb4"></div>
                            </div>
                            <p class="field-error" id="rf-pass-err">Password must be at least 8 characters.</p>
                        </div>

                        <div>
                            <div class="field" id="rf-confirm-wrap">
                                <span class="material-symbols-outlined icon">shield</span>
                                <input id="rf-confirm" type="password" placeholder="Confirm password"
                                    autocomplete="new-password" style="padding-right:46px;" />
                                <button type="button" class="suffix" onclick="togglePass('rf-confirm', this)"
                                    tabindex="-1">
                                    <span class="material-symbols-outlined"
                                        style="font-size:19px;">visibility_off</span>
                                </button>
                            </div>
                            <p class="field-error" id="rf-confirm-err">Passwords do not match.</p>
                        </div>

                    </div>

                    <button type="submit" class="btn-cta mt-6" id="registerBtn" onclick="ripple(event)">
                        <span id="registerBtnContent" class="flex items-center justify-center gap-2">Create
                            Account</span>
                    </button>

                    <p class="text-center mt-5" style="font-size:12.5px;color:#555;">
                        Already have an account?
                        <a href="#" onclick="switchTab('login');return false;"
                            style="color:#ff5d2e;font-weight:600;text-decoration:none;margin-left:2px;">Sign in</a>
                    </p>
                </form>
            </div>

            <!-- ─── Divider ────────────────────────────────────── -->
            <div class="flex items-center gap-3 mt-8">
                <div class="flex-1 h-px" style="background:#282828;"></div>
                <span
                    style="font-size:10px;letter-spacing:0.18em;text-transform:uppercase;font-weight:600;color:#444;">or
                    continue with</span>
                <div class="flex-1 h-px" style="background:#282828;"></div>
            </div>

            <!-- ─── Social ─────────────────────────────────────── -->

        </div><!-- /auth-card -->
    </main>

    <!-- ═══ Footer ════════════════════════════════════════════════════════════════ -->
    <footer
        class="fixed bottom-0 left-0 right-0 z-40 flex flex-col sm:flex-row justify-between items-center px-8 sm:px-12 py-6 pointer-events-none"
        style="background:linear-gradient(to top,rgba(10,10,10,0.9),transparent);">
        <p style="font-family:'Epilogue',sans-serif;font-size:10px;letter-spacing:0.35em;font-weight:900;font-style:italic;color:#fd8b00;opacity:0.7;text-transform:uppercase;margin-bottom:8px;"
            class="sm:mb-0">
            Become the strongest version of yourself.
        </p>
        <div class="flex gap-6 pointer-events-auto">
            <a href="#"
                style="font-size:10px;letter-spacing:0.18em;font-weight:600;color:#444;text-transform:uppercase;text-decoration:none;transition:color 0.2s;"
                onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#444'">Privacy</a>
            <a href="#"
                style="font-size:10px;letter-spacing:0.18em;font-weight:600;color:#444;text-transform:uppercase;text-decoration:none;transition:color 0.2s;"
                onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#444'">Terms</a>
            <a href="#"
                style="font-size:10px;letter-spacing:0.18em;font-weight:600;color:#444;text-transform:uppercase;text-decoration:none;transition:color 0.2s;"
                onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#444'">Support</a>
        </div>
    </footer>

    <!-- ═══ Toast ══════════════════════════════════════════════════════════════════ -->
    <div id="toast" class="toast">
        <span class="material-symbols-outlined toast-icon" id="toastIcon"
            style="font-variation-settings:'FILL' 1,'wght' 400;">check_circle</span>
        <span id="toastMsg"></span>
    </div>

    <!-- ═══ Scripts ═══════════════════════════════════════════════════════════════ -->
    <script>
        /* ── Config ── */
        const AUTH_ENDPOINT = 'auth.php'; // ← point to your auth.php

        /* ── Tab switching ───────────────────────────────────────────────────────── */
        let currentTab = 'login';

        function switchTab(tab) {
            if (tab === currentTab) return;
            currentTab = tab;

            const track = document.getElementById('tabTrack');
            const loginTab = document.getElementById('tabLogin');
            const regTab = document.getElementById('tabRegister');
            const loginPan = document.getElementById('panelLogin');
            const regPan = document.getElementById('panelRegister');
            const subtitle = document.getElementById('cardSubtitle');

            if (tab === 'login') {
                track.style.left = '0';
                loginTab.style.color = '#ff5d2e';
                regTab.style.color = '#444';
                loginPan.classList.add('active');
                regPan.classList.remove('active');
                loginPan.classList.remove('animate-slide-left');
                loginPan.classList.add('animate-slide-right');
                subtitle.textContent = 'Sign in to your account';
            } else {
                track.style.left = '50%';
                loginTab.style.color = '#444';
                regTab.style.color = '#ff5d2e';
                regPan.classList.add('active');
                loginPan.classList.remove('active');
                regPan.classList.remove('animate-slide-right');
                regPan.classList.add('animate-slide-left');
                subtitle.textContent = 'Create your free account';
            }
            clearErrors();
        }

        /* ── Password visibility ─────────────────────────────────────────────────── */
        function togglePass(inputId, btn) {
            const inp = document.getElementById(inputId);
            const icon = btn.querySelector('.material-symbols-outlined');
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.textContent = 'visibility';
                icon.style.color = '#ff5d2e';
            } else {
                inp.type = 'password';
                icon.textContent = 'visibility_off';
                icon.style.color = '';
            }
        }

        /* ── Password strength ───────────────────────────────────────────────────── */
        function updateStrength(val) {
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const colors = ['', '#ef4444', '#fb923c', '#facc15', '#22c55e'];
            const bars = ['sb1', 'sb2', 'sb3', 'sb4'];
            bars.forEach((id, i) => {
                const el = document.getElementById(id);
                el.style.background = i < score ? colors[score] : 'rgba(255,255,255,0.08)';
            });
        }

        /* ── Ripple ──────────────────────────────────────────────────────────────── */
        function ripple(e) {
            const btn = e.currentTarget;
            const rect = btn.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            const r = document.createElement('span');
            r.className = 'ripple';
            r.style.cssText = `width:${size}px;height:${size}px;left:${x}px;top:${y}px;`;
            btn.appendChild(r);
            setTimeout(() => r.remove(), 600);
        }

        /* ── Toast ───────────────────────────────────────────────────────────────── */
        let toastTimer = null;
        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            const icon = document.getElementById('toastIcon');
            const text = document.getElementById('toastMsg');

            toast.className = `toast ${type}`;
            icon.textContent = type === 'success' ? 'check_circle' : 'error';
            text.textContent = msg;

            requestAnimationFrame(() => {
                requestAnimationFrame(() => toast.classList.add('show'));
            });

            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => toast.classList.remove('show'), 3500);
        }

        /* ── Field error helpers ─────────────────────────────────────────────────── */
        function setError(wrapId, errId, show) {
            document.getElementById(wrapId).classList.toggle('error-field', show);
            if (errId) document.getElementById(errId).classList.toggle('show', show);
        }
        function clearErrors() {
            ['lf-email-wrap', 'lf-pass-wrap', 'rf-name-wrap', 'rf-email-wrap', 'rf-pass-wrap', 'rf-confirm-wrap'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.remove('error-field');
            });
            ['lf-email-err', 'lf-pass-err', 'rf-name-err', 'rf-email-err', 'rf-pass-err', 'rf-confirm-err'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.remove('show');
            });
        }

        /* ── Loading state ───────────────────────────────────────────────────────── */
        function setLoading(btnId, loading) {
            const btn = document.getElementById(btnId);
            btn.disabled = loading;
            btn.style.opacity = loading ? '0.7' : '1';
        }

        // ═══ Form Submissions ════════════════════════════════════════════════════
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            if (window.app && typeof window.app.login === 'function') return;

            clearErrors();
            const email = document.getElementById('lf-email').value.trim();
            const pass = document.getElementById('lf-pass').value;

            let valid = true;
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setError('lf-email-wrap', 'lf-email-err', true); valid = false; }
            if (!pass) { setError('lf-pass-wrap', 'lf-pass-err', true); valid = false; }
            if (!valid) return;

            setLoading('loginBtn', true);
            const result = await callAuth('login', { email, password: pass });
            setLoading('loginBtn', false);

            if (result.success) {
                showToast(result.message || 'Welcome back!', 'success');
                setTimeout(() => window.location.reload(), 1200);
            } else {
                showToast(result.message || 'Login failed.', 'error');
                if (result.message?.toLowerCase().includes('password')) {
                    setError('lf-pass-wrap', 'lf-pass-err', true);
                    document.getElementById('lf-pass-err').textContent = result.message;
                } else {
                    setError('lf-email-wrap', 'lf-email-err', true);
                    document.getElementById('lf-email-err').textContent = result.message;
                }
            }
        });

        /* ── Register ────────────────────────────────────────────────────────────── */
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            clearErrors();

            const name = document.getElementById('regName').value.trim();
            const email = document.getElementById('regEmail').value.trim();
            const pass = document.getElementById('regPass').value;
            const confirm = document.getElementById('rf-confirm').value;

            let valid = true;
            if (name.length < 2) { setError('rf-name-wrap', 'rf-name-err', true); valid = false; }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setError('rf-email-wrap', 'rf-email-err', true); valid = false; }
            if (pass.length < 8) { setError('rf-pass-wrap', 'rf-pass-err', true); valid = false; }
            if (pass !== confirm) { setError('rf-confirm-wrap', 'rf-confirm-err', true); valid = false; }
            if (!valid) return;

            setLoading('registerBtn', 'registerBtnContent', true);
            const result = await callAuth('register', { name, email, password: pass });
            setLoading('registerBtn', 'registerBtnContent', false);

            if (result.success) {
                showToast(result.message || 'Account created!', 'success');
                setTimeout(() => switchTab('login'), 1400);
            } else {
                showToast(result.message || 'Registration failed.', 'error');
                if (result.message?.toLowerCase().includes('email')) {
                    setError('rf-email-wrap', 'rf-email-err', true);
                    document.getElementById('rf-email-err').textContent = result.message;
                }
            }
        });

        /* ── Check existing session on load ─────────────────────────────────────── */
        (async () => {
            try {
                const r = await fetch(`${AUTH_ENDPOINT}?action=check`);
                const d = await r.json();
                if (d.logged_in) window.location.href = 'dashboard.php';
            } catch (_) { /* silently continue */ }
        })();
        // Safeguard for standalone testing
        window.addEventListener('load', () => {
            if (!document.getElementById('mainApp')) {
                document.getElementById('authModal').classList.remove('hidden');
            }
        });
    </script>
</body>
</html>