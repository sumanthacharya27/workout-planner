<?php
// ============================================
// LOGIN PAGE
// ============================================
// login.php
// Handles admin authentication

session_start();

// If already logged in, redirect to app
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/db.php';
    
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required';
    } else {
        try {
            // Query user
            $stmt = $pdo->prepare('SELECT id, username, password, role FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            // Verify credentials
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Generate CSRF token
                if (empty($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid username or password';
            }
        } catch (PDOException $e) {
            $error = 'Login failed: ' . $e->getMessage();
        }
    }
}
?>

<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{
  --primary:#FF6B35;--primary-dark:#E5562F;--primary-light:#FF8C5A;
  --secondary:#F7931E;--accent:#FFD23F;--success:#06D6A0;--danger:#EF476F;
  --dark:#1A1A2E;--dark-alt:#16213E;--light:#FFFFFF;
  --gray-100:#F8F9FA;--gray-200:#E9ECEF;--gray-300:#DEE2E6;--gray-700:#495057;
  --gradient-1:linear-gradient(135deg,#FF6B35 0%,#F7931E 100%);
  --gradient-2:linear-gradient(135deg,#06D6A0 0%,#1B98E0 100%);
  --shadow-md:0 4px 16px rgba(0,0,0,0.15);
  --shadow-lg:0 8px 32px rgba(0,0,0,0.2);
  --shadow-glow:0 0 20px rgba(255,107,53,0.3);
}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI','Roboto',sans-serif}

.auth-wrapper{
  background:linear-gradient(135deg,#1A1A2E 0%,#16213E 50%,#0F3460 100%);
  min-height:700px;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:2.5rem 1rem;
}

.auth-box{
  background:#fff;
  border-radius:24px;
  width:100%;
  max-width:460px;
  padding:3rem 2.5rem;
  box-shadow:var(--shadow-lg);
  position:relative;
  overflow:hidden;
}

.auth-box::before{
  content:'';
  position:absolute;
  top:-60px;right:-60px;
  width:220px;height:220px;
  background:radial-gradient(circle,rgba(255,107,53,0.08) 0%,transparent 70%);
  pointer-events:none;
}

.auth-logo{
  font-size:2rem;
  font-weight:800;
  background:var(--gradient-1);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  background-clip:text;
  letter-spacing:-0.5px;
  text-transform:uppercase;
  margin-bottom:0.25rem;
}

.auth-subtitle{
  color:var(--gray-700);
  font-size:0.95rem;
  font-weight:500;
  margin-bottom:2rem;
}

.tab-row{
  display:flex;
  border-bottom:3px solid var(--gray-200);
  margin-bottom:2rem;
}

.tab-btn{
  flex:1;
  background:none;
  border:none;
  border-bottom:3px solid transparent;
  margin-bottom:-3px;
  padding:0.85rem 0;
  font-size:0.95rem;
  font-weight:700;
  color:var(--gray-700);
  cursor:pointer;
  text-transform:uppercase;
  letter-spacing:0.5px;
  transition:all 0.3s ease;
}

.tab-btn.active{
  color:var(--primary);
  border-bottom-color:var(--primary);
}

.tab-btn:hover:not(.active){color:var(--primary-light)}

.panel{display:none;animation:fadeInUp 0.3s ease}
.panel.active{display:block}

@keyframes fadeInUp{
  from{opacity:0;transform:translateY(16px)}
  to{opacity:1;transform:translateY(0)}
}

.form-group{margin-bottom:1.5rem}

.form-group label{
  display:block;
  font-weight:700;
  font-size:0.8rem;
  text-transform:uppercase;
  letter-spacing:0.5px;
  color:var(--dark);
  margin-bottom:0.6rem;
}

.form-group input{
  width:100%;
  padding:0.9rem 1.1rem;
  border:3px solid var(--gray-300);
  border-radius:12px;
  font-size:0.95rem;
  font-family:inherit;
  color:var(--dark);
  background:#fff;
  transition:all 0.3s cubic-bezier(0.4,0,0.2,1);
}

.form-group input:focus{
  outline:none;
  border-color:var(--primary);
  box-shadow:0 0 0 4px rgba(255,107,53,0.1);
  transform:translateY(-2px);
}

.form-group input.error{border-color:var(--danger);box-shadow:0 0 0 4px rgba(239,71,111,0.1)}

.pw-wrap{position:relative}
.pw-wrap input{padding-right:44px}
.pw-eye{
  position:absolute;right:12px;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;color:var(--gray-700);padding:4px;
  display:flex;align-items:center;
}
.pw-eye:hover{color:var(--primary)}

.input-error{font-size:0.8rem;color:var(--danger);margin-top:5px;display:none;font-weight:600}
.input-error.show{display:block}

.strength-bar{display:flex;gap:4px;margin-top:8px}
.strength-seg{flex:1;height:4px;border-radius:3px;background:var(--gray-200);transition:background 0.3s}
.strength-seg.weak{background:var(--danger)}
.strength-seg.fair{background:var(--secondary)}
.strength-seg.good{background:var(--accent)}
.strength-seg.strong{background:var(--success)}
.strength-lbl{font-size:0.78rem;color:var(--gray-700);margin-top:5px;font-weight:500}

.form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}

.forgot-link{
  display:block;text-align:right;font-size:0.82rem;
  font-weight:700;color:var(--primary);text-decoration:none;
  margin-top:-0.75rem;margin-bottom:1.5rem;cursor:pointer;
  text-transform:uppercase;letter-spacing:0.3px;
}
.forgot-link:hover{color:var(--primary-dark);text-decoration:underline}

.submit-btn{
  width:100%;padding:1rem;
  background:var(--gradient-1);
  color:#fff;border:none;
  border-radius:12px;
  font-size:1rem;font-weight:700;
  text-transform:uppercase;letter-spacing:0.5px;
  cursor:pointer;
  transition:all 0.3s cubic-bezier(0.4,0,0.2,1);
  box-shadow:var(--shadow-md);
  position:relative;overflow:hidden;
}

.submit-btn:hover{transform:translateY(-3px);box-shadow:var(--shadow-lg),var(--shadow-glow)}
.submit-btn:active{transform:scale(0.99)}

.submit-btn .btn-label{display:inline-block;transition:opacity 0.2s}
.submit-btn .spinner{
  display:none;width:20px;height:20px;
  border:3px solid rgba(255,255,255,0.3);border-top-color:#fff;
  border-radius:50%;animation:spin 0.7s linear infinite;margin:0 auto;
}
@keyframes spin{to{transform:rotate(360deg)}}

.divider{
  display:flex;align-items:center;gap:12px;
  margin:1.75rem 0;font-size:0.8rem;
  font-weight:700;color:var(--gray-700);
  text-transform:uppercase;letter-spacing:0.5px;
}
.divider::before,.divider::after{content:'';flex:1;height:2px;background:var(--gray-200)}

.social-btn{
  width:100%;height:48px;
  display:flex;align-items:center;justify-content:center;gap:10px;
  background:var(--gray-100);
  border:3px solid var(--gray-300);
  border-radius:12px;
  font-family:inherit;font-size:0.9rem;font-weight:700;
  color:var(--dark);cursor:pointer;
  text-transform:uppercase;letter-spacing:0.3px;
  transition:all 0.3s cubic-bezier(0.4,0,0.2,1);
}
.social-btn:hover{border-color:var(--primary);background:#fff;transform:translateY(-2px);box-shadow:var(--shadow-md)}

.checkbox-row{display:flex;align-items:flex-start;gap:10px;margin:1.25rem 0}
.checkbox-row input[type=checkbox]{
  width:18px;height:18px;margin-top:2px;
  accent-color:var(--primary);flex-shrink:0;cursor:pointer;
}
.checkbox-row label{font-size:0.85rem;color:var(--gray-700);font-weight:500;line-height:1.5;cursor:pointer}
.checkbox-row label a{color:var(--primary);font-weight:700;text-decoration:none}
.checkbox-row label a:hover{text-decoration:underline}

.success-state{
  text-align:center;padding:1.5rem 0;display:none;
  animation:fadeInUp 0.3s ease;
}

.success-icon-wrap{
  width:72px;height:72px;margin:0 auto 1.25rem;
  background:linear-gradient(135deg,#06D6A0 0%,#1B98E0 100%);
  border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 8px 24px rgba(6,214,160,0.35);
}

.success-title{
  font-size:1.5rem;font-weight:800;
  background:var(--gradient-1);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
  margin-bottom:0.5rem;
}

.success-sub{font-size:0.95rem;color:var(--gray-700);font-weight:500;line-height:1.6}

.success-btn{
  display:inline-block;margin-top:1.5rem;
  padding:0.9rem 2.5rem;
  background:var(--gradient-1);color:#fff;
  border:none;border-radius:12px;
  font-family:inherit;font-size:0.9rem;font-weight:700;
  text-transform:uppercase;letter-spacing:0.5px;
  cursor:pointer;
  box-shadow:var(--shadow-md);
  transition:all 0.3s ease;
}
.success-btn:hover{transform:translateY(-3px);box-shadow:var(--shadow-lg),var(--shadow-glow)}
</style>

<div class="auth-wrapper">
  <div class="auth-box">
    <div class="auth-logo">FitTrack</div>
    <div class="auth-subtitle">Your personal workout companion</div>

    <div class="tab-row">
      <button class="tab-btn active" onclick="switchTab('login')">Log in</button>
      <button class="tab-btn" onclick="switchTab('register')">Sign up</button>
    </div>

    <div class="panel active" id="panel-login">
        <form method="POST" action="">
      <?php if (!empty($error)): ?>
        <div style="background:#fef2f2;border:2px solid #EF476F;border-radius:10px;padding:0.75rem 1rem;margin-bottom:1.25rem;font-size:0.88rem;font-weight:600;color:#c0392b;"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <div class="form-group">
        <label>Username</label>
        <input type="text" id="l-email" name="username" placeholder="Enter your username" autocomplete="username" />
        <div class="input-error" id="l-email-err">Please enter your username</div>
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="pw-wrap">
          <input type="password" id="l-pw" name="password" placeholder="Enter your password" autocomplete="current-password" />
          <button class="pw-eye" onclick="togglePw('l-pw',this)" tabindex="-1">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
        <div class="input-error" id="l-pw-err">Password is required</div>
      </div>
      <a class="forgot-link" onclick="alert('Reset link sent!')">Forgot password?</a>
      <button class="submit-btn" id="l-btn" type="submit">
        <span class="btn-label" id="l-label">Log In</span>
        <div class="spinner" id="l-spin"></div>
      </button>
      <div class="divider">or continue with</div>
      <button class="social-btn" onclick="socialAuth()">
        <svg width="18" height="18" viewBox="0 0 24 24" style="flex-shrink:0"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
        Google
      </button>
</form>
    </div>


    <div class="panel" id="panel-register">
      <div id="r-server-err" style="display:none;background:#fef2f2;border:2px solid #EF476F;border-radius:10px;padding:0.75rem 1rem;margin-bottom:1.25rem;font-size:0.88rem;font-weight:600;color:#c0392b;"></div>
      <div class="form-group">
        <label>Username</label>
        <input type="text" id="r-username" placeholder="Choose a username" autocomplete="username" />
        <div class="input-error" id="r-username-err">Username must be at least 3 characters</div>
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="pw-wrap">
          <input type="password" id="r-pw" placeholder="Create a password (min 6 chars)" oninput="checkStrength(this.value)" autocomplete="new-password" />
          <button class="pw-eye" onclick="togglePw('r-pw',this)" tabindex="-1">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
        <div class="strength-bar">
          <div class="strength-seg" id="s1"></div>
          <div class="strength-seg" id="s2"></div>
          <div class="strength-seg" id="s3"></div>
          <div class="strength-seg" id="s4"></div>
        </div>
        <div class="strength-lbl" id="s-lbl">Use 8+ characters with numbers and symbols</div>
        <div class="input-error" id="r-pw-err">Password must be at least 6 characters</div>
      </div>
      <div class="form-group">
        <label>Confirm password</label>
        <div class="pw-wrap">
          <input type="password" id="r-pw2" placeholder="Repeat your password" autocomplete="new-password" />
          <button class="pw-eye" onclick="togglePw('r-pw2',this)" tabindex="-1">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
        <div class="input-error" id="r-pw2-err">Passwords do not match</div>
      </div>
      <div class="checkbox-row">
        <input type="checkbox" id="terms" />
        <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
      </div>
      <button class="submit-btn" id="r-btn" type="button" onclick="doRegister()">
        <span class="btn-label" id="r-label">Create Account</span>
        <div class="spinner" id="r-spin"></div>
      </button>
    </div>

    <div class="success-state" id="success-state">
      <div class="success-icon-wrap">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <div class="success-title" id="s-title">You're in!</div>
      <div class="success-sub" id="s-sub">Welcome to FitTrack. Let's crush your goals.</div>
      <button class="success-btn" onclick="resetAll()">Back to login</button>
    </div>

  </div>
</div>

<script>
function switchTab(t){
  document.querySelectorAll('.tab-btn').forEach((b,i)=>b.classList.toggle('active',(i===0&&t==='login')||(i===1&&t==='register')));
  document.getElementById('panel-login').classList.toggle('active',t==='login');
  document.getElementById('panel-register').classList.toggle('active',t==='register');
  document.getElementById('success-state').style.display='none';
}

function togglePw(id,btn){
  const inp=document.getElementById(id);
  const show=inp.type==='text';
  inp.type=show?'password':'text';
  btn.innerHTML=show
    ?'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>'
    :'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
}

function checkStrength(v){
  ['s1','s2','s3','s4'].forEach(s=>{document.getElementById(s).className='strength-seg'});
  const lbl=document.getElementById('s-lbl');
  if(!v){lbl.textContent='Use 8+ characters with numbers and symbols';return 0;}
  let sc=0;
  if(v.length>=8)sc++;
  if(/[A-Z]/.test(v))sc++;
  if(/[0-9]/.test(v))sc++;
  if(/[^A-Za-z0-9]/.test(v))sc++;
  const cls=['','weak','fair','good','strong'];
  const tips=['','Add numbers & symbols','Add uppercase letters','Add symbols','Strong password!'];
  for(let i=0;i<sc;i++)document.getElementById('s'+(i+1)).classList.add(cls[sc]);
  lbl.textContent=tips[sc]||'';
  return sc;
}

function setErr(id,show,inp){
  document.getElementById(id).classList.toggle('show',show);
  if(inp){const el=document.getElementById(inp);el&&el.classList.toggle('error',show);}
}

function isEmail(v){return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)}

function loading(p,on){
  document.getElementById(p+'-label').style.display=on?'none':'inline';
  document.getElementById(p+'-spin').style.display=on?'block':'none';
  document.getElementById(p+'-btn').disabled=on;
}

function showSuccess(title,sub){
  ['panel-login','panel-register'].forEach(id=>document.getElementById(id).classList.remove('active'));
  const s=document.getElementById('success-state');
  s.style.display='block';
  document.getElementById('s-title').textContent=title;
  document.getElementById('s-sub').textContent=sub;
}

function resetAll(){
  document.getElementById('success-state').style.display='none';
  switchTab('login');
}

function doLogin(){
  const username=document.getElementById('l-email').value.trim();
  const pw=document.getElementById('l-pw').value;
  setErr('l-email-err',false,'l-email');setErr('l-pw-err',false,'l-pw');
  let ok=true;
  if(!username){setErr('l-email-err',true,'l-email');ok=false;}
  if(!pw){setErr('l-pw-err',true,'l-pw');ok=false;}
  if(!ok)return;
}

async function doRegister(){
  const username=document.getElementById('r-username').value.trim();
  const pw=document.getElementById('r-pw').value;
  const pw2=document.getElementById('r-pw2').value;
  const terms=document.getElementById('terms').checked;
  const serverErr=document.getElementById('r-server-err');

  // Clear previous errors
  setErr('r-username-err',false,'r-username');
  setErr('r-pw-err',false,'r-pw');
  setErr('r-pw2-err',false,'r-pw2');
  serverErr.style.display='none';

  let ok=true;
  if(username.length<3){setErr('r-username-err',true,'r-username');ok=false;}
  if(pw.length<6){setErr('r-pw-err',true,'r-pw');ok=false;}
  if(pw!==pw2||!pw2){setErr('r-pw2-err',true,'r-pw2');ok=false;}
  if(!terms){alert('Please agree to the Terms of Service to continue.');ok=false;}
  if(!ok)return;

  loading('r',true);
  try {
    const res=await fetch('api/register.php',{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify({username,password:pw})
    });
    const data=await res.json();
    loading('r',false);
    if(data.success){
      // Registered + auto-logged-in → go to app
      window.location.href='index.php';
    } else {
      serverErr.textContent=data.error||'Registration failed. Please try again.';
      serverErr.style.display='block';
    }
  } catch(e){
    loading('r',false);
    serverErr.textContent='Network error. Please try again.';
    serverErr.style.display='block';
  }
}

function socialAuth(){
  loading('l',true);
  setTimeout(()=>{loading('l',false);showSuccess("Welcome!","Signed in with Google successfully.");},1400);
}
</script>
