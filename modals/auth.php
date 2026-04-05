<?php
/**
 * Auth Backend Bridge — modals/auth.php
 * Handles AJAX POST requests for login, register, and session status checks.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_GET['action']) && $_GET['action'] === 'check')) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');

    try {
        // Load shared helpers and auth class
        require_once dirname(__DIR__) . '/includes/response.php';
        require_once dirname(__DIR__) . '/includes/db.php';
        require_once dirname(__DIR__) . '/includes/auth.php';

        // $db and $auth are instantiated at the bottom of their respective files
        if (!isset($db) || !($db->getConnection() instanceof mysqli)) {
            throw new RuntimeException('Database connection failed.');
        }

        // Parse JSON body (POST) or fall back to query string (GET check)
        $data = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo json_encode(['success' => false, 'message' => 'Invalid JSON input.']);
                exit;
            }
        }

        $action = $data['action'] ?? $_GET['action'] ?? '';

        switch ($action) {
            case 'login':
                $result = $auth->login(
                    $data['email']    ?? '',
                    $data['password'] ?? ''
                );
                echo json_encode($result);
                break;

            case 'register':
                $result = $auth->register(
                    $data['email']    ?? '',
                    $data['password'] ?? '',
                    $data['name']     ?? ''
                );
                echo json_encode($result);
                break;

            case 'check':
                echo json_encode([
                    'success'   => true,
                    'logged_in' => $auth->isLoggedIn(),
                    'user_name' => $auth->getCurrentUserName(),
                ]);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        }

    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage(),
        ]);
    }

    exit;
}
// ─── HTML page (GET request, no action param) ───────────────────────────────
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>THE KINETIC EDITORIAL — Auth</title>

  <link rel="stylesheet" href="<?php echo (basename($_SERVER['PHP_SELF']) === 'auth.php' ? '../' : ''); ?>public/tailwind.css" />
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:ital,wght@0,700;0,800;0,900;1,700;1,800;1,900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

  <!-- <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand:   { DEFAULT: '#ff5d2e', dim: '#cc3d12' },
            amber:   { DEFAULT: '#fd8b00' },
            surface: { DEFAULT: '#0a0a0a', card: '#0e0e0e', border: '#282828' },
            ink:     { DEFAULT: '#ffffff', muted: '#888888', faint: '#444444' },
          },
          fontFamily: {
            headline: ['Epilogue', 'sans-serif'],
            body:     ['DM Sans', 'sans-serif'],
          },
          keyframes: {
            fadeUp:     { '0%': { opacity: 0, transform: 'translateY(18px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
            slideLeft:  { '0%': { opacity: 0, transform: 'translateX(24px)' },  '100%': { opacity: 1, transform: 'translateX(0)' } },
            slideRight: { '0%': { opacity: 0, transform: 'translateX(-24px)' }, '100%': { opacity: 1, transform: 'translateX(0)' } },
            rippleAnim: { 'to': { transform: 'scale(4)', opacity: '0' } },
            spin:       { 'to': { transform: 'rotate(360deg)' } },
          },
          animation: {
            'fade-up':    'fadeUp 0.5s ease both',
            'slide-left': 'slideLeft 0.35s cubic-bezier(0.4,0,0.2,1) both',
            'slide-right':'slideRight 0.35s cubic-bezier(0.4,0,0.2,1) both',
            'ripple':     'rippleAnim 0.5s linear forwards',
            'spin-fast':  'spin 0.7s linear infinite',
          },
        },
      },
    };
  </script> -->


  <style>
    /* ── Base ── */
    *, *::before, *::after { box-sizing: border-box; }
    body { font-family: 'DM Sans', sans-serif; background: #0a0a0a; color: #fff; overflow-x: hidden; }

    /* ── Grain ── */
    body::before {
      content: '';
      position: fixed; inset: 0; z-index: 0; pointer-events: none;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");
      opacity: 0.6;
    }

    /* ── Hero background image ── */
    .hero-bg { position: fixed; inset: 0; z-index: 0; }
    .hero-bg img { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.22) saturate(0.5); }
    .hero-bg::after {
      content: '';
      position: absolute; inset: 0;
      background:
        radial-gradient(ellipse 70% 60% at 50% 80%, rgba(255,93,46,0.08) 0%, transparent 70%),
        linear-gradient(to top,    #0a0a0a 0%, transparent 50%),
        linear-gradient(to bottom, #0a0a0a 0%, transparent 30%);
    }

    /* ── Card ── */
    .auth-card {
      background: rgba(14,14,14,0.88);
      backdrop-filter: blur(28px) saturate(160%);
      -webkit-backdrop-filter: blur(28px) saturate(160%);
      border: 1px solid rgba(255,255,255,0.07);
      box-shadow: 0 24px 64px rgba(0,0,0,0.7), 0 1px 0 rgba(255,255,255,0.05) inset;
    }

    /* ── Tab underline ── */
    .tab-track {
      position: absolute; bottom: -1px; height: 2px;
      background: linear-gradient(90deg, #ff5d2e, #fd8b00);
      border-radius: 2px;
      transition: left 0.35s cubic-bezier(0.4,0,0.2,1), width 0.35s cubic-bezier(0.4,0,0.2,1);
    }

    /* ── Input field wrapper ── */
    .field {
      position: relative; border-radius: 12px;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.08);
      transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .field:focus-within {
      background: rgba(255,93,46,0.04);
      border-color: rgba(255,93,46,0.45);
      box-shadow: 0 0 0 3px rgba(255,93,46,0.1);
    }
    .field.field-error {
      border-color: rgba(255,80,80,0.6);
      box-shadow: 0 0 0 3px rgba(255,80,80,0.1);
    }
    .field input {
      width: 100%; padding: 14px 14px 14px 46px;
      background: transparent; border: none; outline: none;
      color: #fff; font-family: 'DM Sans', sans-serif; font-size: 14px;
    }
    .field input::placeholder { color: #555; }
    .field .field-icon {
      position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
      color: #555; font-size: 19px; pointer-events: none;
      transition: color 0.2s;
      font-variation-settings: 'FILL' 0, 'wght' 350, 'GRAD' 0, 'opsz' 22;
    }
    .field:focus-within .field-icon { color: #ff5d2e; }
    .field .field-suffix {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      background: none; border: none; cursor: pointer; color: #555;
      padding: 4px; font-size: 19px; transition: color 0.2s; line-height: 1;
      font-variation-settings: 'FILL' 0, 'wght' 350, 'GRAD' 0, 'opsz' 22;
    }
    .field .field-suffix:hover { color: #aaa; }

    /* ── Strength bars ── */
    .strength-bar { height: 3px; border-radius: 2px; transition: background 0.4s ease; flex: 1; }

    /* ── CTA button ── */
    .btn-cta {
      width: 100%;
      background: linear-gradient(135deg, #ff5d2e 0%, #fd6422 50%, #fd8b00 100%);
      color: #fff;
      font-family: 'Epilogue', sans-serif; font-weight: 900; font-style: italic;
      letter-spacing: 0.15em; text-transform: uppercase;
      border: none; border-radius: 100px; padding: 16px 24px; font-size: 14px;
      cursor: pointer; position: relative; overflow: hidden;
      transition: transform 0.15s, box-shadow 0.2s, filter 0.2s;
      box-shadow: 0 8px 30px rgba(255,93,46,0.35);
    }
    .btn-cta:hover { filter: brightness(1.1); box-shadow: 0 12px 40px rgba(255,93,46,0.5); transform: translateY(-1px); }
    .btn-cta:active { transform: scale(0.97) translateY(0); }
    .btn-cta:disabled { opacity: 0.6; cursor: not-allowed; transform: none; filter: none; }

    /* ── Ripple ── */
    .ripple-el {
      position: absolute; border-radius: 50%;
      background: rgba(255,255,255,0.25);
      transform: scale(0);
      animation: rippleAnim 0.5s linear forwards;
      pointer-events: none;
    }

    /* ── Spinner ── */
    .spinner {
      width: 18px; height: 18px;
      border: 2px solid rgba(255,255,255,0.3);
      border-top-color: #fff; border-radius: 50%;
      animation: spin 0.7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Toast ── */
    .toast {
      position: fixed; bottom: 28px; left: 50%;
      transform: translateX(-50%) translateY(80px);
      min-width: 280px; max-width: 400px;
      padding: 14px 20px; border-radius: 14px;
      font-size: 13.5px; font-weight: 500;
      display: flex; align-items: center; gap: 10px;
      backdrop-filter: blur(20px);
      border: 1px solid; z-index: 9999;
      transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1), opacity 0.3s ease;
      opacity: 0; pointer-events: none;
    }
    .toast.show { transform: translateX(-50%) translateY(0); opacity: 1; pointer-events: auto; }
    .toast.success { background: rgba(20,40,20,0.92); border-color: rgba(60,200,80,0.35); color: #6dff88; }
    .toast.error   { background: rgba(40,10,10,0.92);  border-color: rgba(255,80,80,0.35);  color: #ff8080; }
    .toast-icon { font-size: 18px; flex-shrink: 0; font-variation-settings: 'FILL' 1, 'wght' 400; }

    /* ── Form panels ── */
    .form-panel { display: none; }
    .form-panel.active { display: block; }

    /* ── Field inline error ── */
    .err-msg { font-size: 11.5px; color: #ff7070; margin-top: 5px; padding-left: 4px; display: none; }
    .err-msg.show { display: block; }

    /* ── Material icon defaults ── */
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 350, 'GRAD' 0, 'opsz' 22; }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 4px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #282828; border-radius: 4px; }

    @media (max-height: 700px) {
      .auth-card { max-height: 92vh; overflow-y: auto; }
    }
  </style>
</head>
<body>

<!-- ═══ Background ══════════════════════════════════════════════════ -->
<div class="hero-bg">
  <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuB5GSnkeb8rzAbj22z4UI2o3kAS4Tb8eELoRN0Zg4jzJfCZQKmTE8ucYFmnsg_Dk49SrUCGRCMIhEhpaQr7ko3-JeuIcA7TX6zxFyxKc6P4cAp_GBWrGAXMhwhG5-jSOjQBXw9gRn1a5YUJ_enmr5_dgBzQm7kphy5sEdu4CGJ-lzS7CDbUxO70fQgvtMg5LTQOiXcmAqc0ovgsDFpkecFEa0LMJpvPLSIagv-yj2jCnsfZQM8P2EO-_k32sLBpXO9O4UevhAJT5Bqz"
       alt="Gym interior" />
</div>

<!-- ═══ Top Bar ══════════════════════════════════════════════════════ -->
<header class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-6 sm:px-10 py-5">
  <span class="font-headline font-black italic text-brand tracking-[0.22em] text-[17px]">
    THE KINETIC EDITORIAL
  </span>
  <span class="material-symbols-outlined text-brand text-[22px]"
        style="font-variation-settings:'FILL' 1,'wght' 400;">fitness_center</span>
</header>

<!-- ═══ Main ════════════════════════════════════════════════════════ -->
<main class="relative z-10 min-h-screen flex items-center justify-center px-4 py-24">
  <div class="auth-card w-full max-w-[420px] rounded-2xl p-8 sm:p-10 animate-fade-up">

    <!-- Brand icon -->
    <div class="flex justify-center mb-7">
      <div class="w-[54px] h-[54px] rounded-2xl flex items-center justify-center"
           style="background:rgba(255,93,46,0.12);border:1px solid rgba(255,93,46,0.2);">
        <span class="material-symbols-outlined text-brand text-[28px]"
              style="font-variation-settings:'FILL' 1,'wght' 400;">local_fire_department</span>
      </div>
    </div>

    <!-- Headline -->
    <div class="text-center mb-8">
      <h1 class="font-headline font-black italic text-[clamp(26px,5vw,32px)] leading-[1.1] uppercase tracking-tight">
        Train Hard.<br /><span class="text-brand">Stay Strong.</span>
      </h1>
      <p id="cardSubtitle" class="text-ink-muted text-[13.5px] mt-2">Sign in to your account</p>
    </div>

    <!-- ─── Tab Toggle ─────────────────────────────────────────── -->
    <div id="tabBar" class="relative flex border-b border-surface-border mb-8">
      <button id="tabLogin" onclick="switchTab('login')"
              class="flex-1 pb-3 text-xs font-bold uppercase tracking-widest font-headline text-brand transition-colors duration-200">
        Login
      </button>
      <button id="tabRegister" onclick="switchTab('register')"
              class="flex-1 pb-3 text-xs font-bold uppercase tracking-widest font-headline text-ink-faint transition-colors duration-200">
        Register
      </button>
      <div id="tabTrack" class="tab-track" style="left:0;width:50%;"></div>
    </div>

    <!-- ─── Login Form ─────────────────────────────────────────── -->
    <div id="panelLogin" class="form-panel active">
      <form id="loginForm" novalidate autocomplete="on">
        <div class="space-y-3">

          <!-- Email -->
          <div>
            <div class="field" id="lf-email-wrap">
              <span class="material-symbols-outlined field-icon">mail</span>
              <input id="lf-email" name="email" type="email"
                     placeholder="Email address" autocomplete="email" />
            </div>
            <p class="err-msg" id="lf-email-err">Please enter a valid email.</p>
          </div>

          <!-- Password -->
          <div>
            <div class="field" id="lf-pass-wrap">
              <span class="material-symbols-outlined field-icon">lock</span>
              <input id="lf-pass" name="password" type="password"
                     placeholder="Password" autocomplete="current-password"
                     style="padding-right:46px;" />
              <button type="button" class="field-suffix material-symbols-outlined"
                      onclick="togglePass('lf-pass',this)" tabindex="-1">visibility_off</button>
            </div>
            <p class="err-msg" id="lf-pass-err">Password is required.</p>
          </div>

        </div>

        <!-- Forgot password -->
        <div class="flex justify-end mt-3 mb-6">
          <a href="#"
             class="text-[12px] font-semibold text-amber uppercase tracking-[0.08em] no-underline
                    hover:text-[#ffa733] transition-colors duration-200">
            Forgot password?
          </a>
        </div>

        <button type="submit" id="loginBtn" class="btn-cta" onclick="ripple(event)">
          <span id="loginBtnContent" class="flex items-center justify-center gap-2">Sign In</span>
        </button>

        <p class="text-center mt-5 text-[12.5px] text-ink-faint">
          No account?
          <a href="#" onclick="switchTab('register');return false;"
             class="text-brand font-semibold no-underline ml-0.5">Create one free</a>
        </p>
      </form>
    </div>

    <!-- ─── Register Form ─────────────────────────────────────── -->
    <div id="panelRegister" class="form-panel">
      <form id="registerForm" novalidate autocomplete="on">
        <div class="space-y-3">

          <!-- Name -->
          <div>
            <div class="field" id="rf-name-wrap">
              <span class="material-symbols-outlined field-icon">person</span>
              <input id="rf-name" name="name" type="text"
                     placeholder="Full name" autocomplete="name" />
            </div>
            <p class="err-msg" id="rf-name-err">Name must be at least 2 characters.</p>
          </div>

          <!-- Email -->
          <div>
            <div class="field" id="rf-email-wrap">
              <span class="material-symbols-outlined field-icon">mail</span>
              <input id="rf-email" name="email" type="email"
                     placeholder="Email address" autocomplete="email" />
            </div>
            <p class="err-msg" id="rf-email-err">Please enter a valid email.</p>
          </div>

          <!-- Password + strength -->
          <div>
            <div class="field" id="rf-pass-wrap">
              <span class="material-symbols-outlined field-icon">lock</span>
              <input id="rf-pass" name="password" type="password"
                     placeholder="Password (min 8 chars)" autocomplete="new-password"
                     oninput="updateStrength(this.value)" style="padding-right:46px;" />
              <button type="button" class="field-suffix material-symbols-outlined"
                      onclick="togglePass('rf-pass',this)" tabindex="-1">visibility_off</button>
            </div>
            <div class="flex gap-1 mt-2 px-1">
              <div class="strength-bar" id="sb1" style="background:rgba(255,255,255,0.08);"></div>
              <div class="strength-bar" id="sb2" style="background:rgba(255,255,255,0.08);"></div>
              <div class="strength-bar" id="sb3" style="background:rgba(255,255,255,0.08);"></div>
              <div class="strength-bar" id="sb4" style="background:rgba(255,255,255,0.08);"></div>
            </div>
            <p class="err-msg" id="rf-pass-err">Password must be at least 8 characters.</p>
          </div>

          <!-- Confirm password -->
          <div>
            <div class="field" id="rf-confirm-wrap">
              <span class="material-symbols-outlined field-icon">shield</span>
              <input id="rf-confirm" type="password"
                     placeholder="Confirm password" autocomplete="new-password"
                     style="padding-right:46px;" />
              <button type="button" class="field-suffix material-symbols-outlined"
                      onclick="togglePass('rf-confirm',this)" tabindex="-1">visibility_off</button>
            </div>
            <p class="err-msg" id="rf-confirm-err">Passwords do not match.</p>
          </div>

        </div>

        <button type="submit" id="registerBtn" class="btn-cta mt-6" onclick="ripple(event)">
          <span id="registerBtnContent" class="flex items-center justify-center gap-2">
            Create Account
          </span>
        </button>

        <p class="text-center mt-5 text-[12.5px] text-ink-faint">
          Already have an account?
          <a href="#" onclick="switchTab('login');return false;"
             class="text-brand font-semibold no-underline ml-0.5">Sign in</a>
        </p>
      </form>
    </div>

    <!-- ─── Divider ────────────────────────────────────────────── -->
    <div class="flex items-center gap-3 mt-8">
      <div class="flex-1 h-px bg-surface-border"></div>
      <span class="text-[10px] tracking-[0.18em] uppercase font-semibold text-ink-faint">
        or continue with
      </span>
      <div class="flex-1 h-px bg-surface-border"></div>
    </div>

  </div><!-- /auth-card -->
</main>

<!-- ═══ Footer ══════════════════════════════════════════════════════ -->
<footer class="fixed bottom-0 left-0 right-0 z-40 flex flex-col sm:flex-row justify-between
               items-center px-8 sm:px-12 py-6 pointer-events-none"
        style="background:linear-gradient(to top,rgba(10,10,10,0.9),transparent);">
  <p class="font-headline font-black italic text-amber text-[10px] tracking-[0.35em]
            uppercase opacity-70 mb-2 sm:mb-0">
    Become the strongest version of yourself.
  </p>
  <nav class="flex gap-6 pointer-events-auto">
    <a href="#" class="text-[10px] tracking-[0.18em] font-semibold text-ink-faint
                        uppercase no-underline hover:text-white transition-colors">Privacy</a>
    <a href="#" class="text-[10px] tracking-[0.18em] font-semibold text-ink-faint
                        uppercase no-underline hover:text-white transition-colors">Terms</a>
    <a href="#" class="text-[10px] tracking-[0.18em] font-semibold text-ink-faint
                        uppercase no-underline hover:text-white transition-colors">Support</a>
  </nav>
</footer>

<!-- ═══ Toast ═══════════════════════════════════════════════════════ -->
<div id="toast" class="toast" role="alert" aria-live="polite">
  <span class="material-symbols-outlined toast-icon" id="toastIcon">check_circle</span>
  <span id="toastMsg"></span>
</div>

<!-- ═══ Scripts ═════════════════════════════════════════════════════ -->
<script>
  /**
   * AUTH_ENDPOINT — points back to this same file.
   */
  const AUTH_ENDPOINT = window.location.pathname.endsWith('index.php') || window.location.pathname.endsWith('/') || !window.location.pathname.includes('modals/')
    ? 'modals/auth.php' 
    : 'auth.php';

  // ── Tab switching ───────────────────────────────────────────────
  let currentTab = 'login';

  function switchTab(tab) {
    if (tab === currentTab) return;
    currentTab = tab;

    const track    = document.getElementById('tabTrack');
    const tabLogin = document.getElementById('tabLogin');
    const tabReg   = document.getElementById('tabRegister');
    const panLogin = document.getElementById('panelLogin');
    const panReg   = document.getElementById('panelRegister');
    const subtitle = document.getElementById('cardSubtitle');

    if (tab === 'login') {
      track.style.left = '0';
      tabLogin.style.color = '#ff5d2e';
      tabReg.style.color   = '#444';
      panLogin.classList.add('active', 'animate-slide-right');
      panReg.classList.remove('active', 'animate-slide-left');
      subtitle.textContent = 'Sign in to your account';
    } else {
      track.style.left = '50%';
      tabLogin.style.color = '#444';
      tabReg.style.color   = '#ff5d2e';
      panReg.classList.add('active', 'animate-slide-left');
      panLogin.classList.remove('active', 'animate-slide-right');
      subtitle.textContent = 'Create your free account';
    }
    clearErrors();
  }

  // ── Password visibility ─────────────────────────────────────────
  function togglePass(inputId, btn) {
    const inp = document.getElementById(inputId);
    if (inp.type === 'password') {
      inp.type = 'text';
      btn.textContent = 'visibility';
      btn.style.color = '#ff5d2e';
    } else {
      inp.type = 'password';
      btn.textContent = 'visibility_off';
      btn.style.color = '';
    }
  }

  // ── Password strength ───────────────────────────────────────────
  function updateStrength(val) {
    let score = 0;
    if (val.length >= 8)        score++;
    if (/[A-Z]/.test(val))      score++;
    if (/[0-9]/.test(val))      score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const palette = ['', '#ef4444', '#fb923c', '#facc15', '#22c55e'];
    ['sb1','sb2','sb3','sb4'].forEach((id, i) => {
      document.getElementById(id).style.background = i < score
        ? palette[score]
        : 'rgba(255,255,255,0.08)';
    });
  }

  // ── Ripple ──────────────────────────────────────────────────────
  function ripple(e) {
    const btn  = e.currentTarget;
    const rect = btn.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const el   = document.createElement('span');
    el.className = 'ripple-el';
    el.style.cssText = `width:${size}px;height:${size}px;left:${e.clientX-rect.left-size/2}px;top:${e.clientY-rect.top-size/2}px;`;
    btn.appendChild(el);
    setTimeout(() => el.remove(), 600);
  }

  // ── Toast ────────────────────────────────────────────────────────
  let _toastTimer;
  function showToast(msg, type = 'success') {
    const toast = document.getElementById('toast');
    const icon  = document.getElementById('toastIcon');
    const text  = document.getElementById('toastMsg');
    toast.className = `toast ${type}`;
    icon.textContent = type === 'success' ? 'check_circle' : 'error';
    text.textContent = msg;
    requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.add('show')));
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => toast.classList.remove('show'), 3500);
  }

  // ── Field error helpers ──────────────────────────────────────────
  function setFieldError(wrapId, errId, msg) {
    document.getElementById(wrapId).classList.add('field-error');
    const errEl = document.getElementById(errId);
    if (msg) errEl.textContent = msg;
    errEl.classList.add('show');
  }
  function clearErrors() {
    ['lf-email-wrap','lf-pass-wrap','rf-name-wrap','rf-email-wrap','rf-pass-wrap','rf-confirm-wrap']
      .forEach(id => { const el = document.getElementById(id); if (el) el.classList.remove('field-error'); });
    ['lf-email-err','lf-pass-err','rf-name-err','rf-email-err','rf-pass-err','rf-confirm-err']
      .forEach(id => { const el = document.getElementById(id); if (el) el.classList.remove('show'); });
  }

  // ── Loading state ────────────────────────────────────────────────
  function setLoading(btnId, contentId, loading, label) {
    const btn = document.getElementById(btnId);
    const cnt = document.getElementById(contentId);
    btn.disabled = loading;
    cnt.innerHTML = loading
      ? `<div class="spinner"></div><span style="margin-left:6px">Please wait…</span>`
      : label;
  }

  // ── API helper ───────────────────────────────────────────────────
 async function callAuth(action, data) {
  try {
    const res = await fetch(AUTH_ENDPOINT, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ action, ...data }),
    });

    const text = await res.text(); // 👈 safer

    try {
      return JSON.parse(text); // try parsing JSON
    } catch {
      console.error("Invalid JSON response:", text); // debug
      return { success: false, message: 'Server returned invalid response' };
    }

  } catch (err) {
    console.error(err);
    return { success: false, message: 'Network error. Please try again.' };
  }
}

  // ── Login ────────────────────────────────────────────────────────
  document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors();

    const email = document.getElementById('lf-email').value.trim();
    const pass  = document.getElementById('lf-pass').value;

    let valid = true;
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setFieldError('lf-email-wrap','lf-email-err'); valid = false; }
    if (!pass)                                        { setFieldError('lf-pass-wrap', 'lf-pass-err');  valid = false; }
    if (!valid) return;

    setLoading('loginBtn', 'loginBtnContent', true, 'Sign In');
    const result = await callAuth('login', { email, password: pass });
    setLoading('loginBtn', 'loginBtnContent', false, 'Sign In');

    if (result.success) {
      showToast(result.message || 'Welcome back!', 'success');
      // Redirect to the main app — adjust path as needed
      setTimeout(() => { window.location.href = 'index.php'; }, 1200);
    } else {
      showToast(result.message || 'Login failed.', 'error');
      const msg = result.message || '';
      if (msg.toLowerCase().includes('password')) {
        setFieldError('lf-pass-wrap', 'lf-pass-err', msg);
      } else {
        setFieldError('lf-email-wrap', 'lf-email-err', msg);
      }
    }
  });

  // ── Register ─────────────────────────────────────────────────────
  document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors();

    const name    = document.getElementById('rf-name').value.trim();
    const email   = document.getElementById('rf-email').value.trim();
    const pass    = document.getElementById('rf-pass').value;
    const confirm = document.getElementById('rf-confirm').value;

    let valid = true;
    if (name.length < 2)                                      { setFieldError('rf-name-wrap',    'rf-name-err');    valid = false; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))           { setFieldError('rf-email-wrap',   'rf-email-err');   valid = false; }
    if (pass.length < 8)                                      { setFieldError('rf-pass-wrap',    'rf-pass-err');    valid = false; }
    if (pass !== confirm)                                     { setFieldError('rf-confirm-wrap', 'rf-confirm-err'); valid = false; }
    if (!valid) return;

    setLoading('registerBtn', 'registerBtnContent', true, 'Create Account');
    const result = await callAuth('register', { name, email, password: pass });
    setLoading('registerBtn', 'registerBtnContent', false, 'Create Account');

    if (result.success) {
      showToast(result.message || 'Account created! Please sign in.', 'success');
      setTimeout(() => switchTab('login'), 1400);
    } else {
      showToast(result.message || 'Registration failed.', 'error');
      const msg = result.message || '';
      if (msg.toLowerCase().includes('email')) {
        setFieldError('rf-email-wrap', 'rf-email-err', msg);
      }
    }
  });

  // ── Check session on page load ────────────────────────────────────
  (async () => {
    // If we're inside index.php, our main app.js handles auth check
    if (window.location.pathname.endsWith('index.php') || window.location.pathname.endsWith('/')) {
      console.log('Skipping standalone auth check in index.php');
      return;
    }
    
    try {
      const res = await fetch(`${AUTH_ENDPOINT}?action=check`, { credentials: 'same-origin' });
      const d   = await res.json();
      // If already logged in, skip the auth page
      if (d.logged_in) window.location.href = 'index.php';
    } catch (_) { /* silently continue to login page */ }
  })();
</script>
</body>
</html>