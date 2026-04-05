<!DOCTYPE html>
<html lang="en">
<head><title></title>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link href="dist/output.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,400;0,600;0,700;0,800;0,900;1,800;1,900&family=DM+Sans:wght@300;400;500&family=Material+Symbols+Outlined:wght,FILL@400,0&display=swap" rel="stylesheet"/>
<style>
  *{box-sizing:border-box;margin:0;padding:0;}
  :root{
    --accent:#FF4D1C;
    --accent2:#FFB347;
    --bg:#0A0A0A;
    --bg2:#111;
    --bg3:#1A1A1A;
    --bg4:#222;
    --border:rgba(255,255,255,0.07);
    --text:#fff;
    --muted:rgba(255,255,255,0.45);
    --card-r:14px;
  }
  body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh;overflow-x:hidden;}
  .icon{font-family:'Material Symbols Outlined';font-weight:400;font-style:normal;line-height:1;letter-spacing:normal;display:inline-block;}
  .cond{font-family:'Barlow Condensed',sans-serif;}

  /* Layout */
  .layout{display:flex;min-height:100vh;}
  .sidebar{width:220px;flex-shrink:0;background:var(--bg2);border-right:1px solid var(--border);display:flex;flex-direction:column;padding:0;position:relative;z-index:10;}
  .main{flex:1;overflow-y:auto;display:flex;flex-direction:column;}

  /* Sidebar */
  .sidebar-logo{padding:28px 24px 24px;border-bottom:1px solid var(--border);}
  .sidebar-logo .wordmark{font-family:'Barlow Condensed',sans-serif;font-weight:900;font-style:italic;font-size:18px;color:var(--accent);letter-spacing:2px;line-height:1;}
  .sidebar-logo .sub{font-size:10px;color:var(--muted);letter-spacing:3px;margin-top:3px;}
  .sidebar-nav{padding:20px 12px;flex:1;}
  .nav-section-label{font-size:10px;font-weight:500;letter-spacing:3px;color:var(--muted);padding:0 12px;margin-bottom:8px;margin-top:16px;}
  .nav-item{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;cursor:pointer;transition:all 0.15s;color:var(--muted);font-size:13px;font-weight:400;text-decoration:none;}
  .nav-item:hover{background:rgba(255,255,255,0.05);color:var(--text);}
  .nav-item.active{background:rgba(255,77,28,0.12);color:var(--accent);}
  .nav-item .icon{font-size:18px;}
  .nav-item .badge{margin-left:auto;background:var(--accent);color:#fff;font-size:10px;font-weight:600;padding:1px 7px;border-radius:20px;}
  .sidebar-bottom{padding:16px 12px;border-top:1px solid var(--border);}
  .user-chip{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;cursor:pointer;transition:all 0.15s;}
  .user-chip:hover{background:rgba(255,255,255,0.05);}
  .avatar{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;flex-shrink:0;}
  .user-chip .name{font-size:12px;font-weight:500;color:var(--text);}
  .user-chip .role{font-size:10px;color:var(--muted);}

  /* Top bar */
  .topbar{display:flex;align-items:center;justify-content:space-between;padding:16px 32px;border-bottom:1px solid var(--border);background:rgba(10,10,10,0.85);backdrop-filter:blur(12px);position:sticky;top:0;z-index:20;}
  .topbar-left{display:flex;flex-direction:column;}
  .topbar-breadcrumb{font-size:11px;color:var(--muted);letter-spacing:2px;margin-bottom:3px;}
  .topbar-title{font-family:'Barlow Condensed',sans-serif;font-weight:900;font-style:italic;font-size:22px;letter-spacing:1px;}
  .topbar-actions{display:flex;align-items:center;gap:10px;}
  .icon-btn{width:36px;height:36px;border-radius:8px;border:1px solid var(--border);background:var(--bg3);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.15s;color:var(--muted);}
  .icon-btn:hover{border-color:rgba(255,255,255,0.15);color:var(--text);background:var(--bg4);}
  .icon-btn .icon{font-size:18px;}
  .cta-btn{display:flex;align-items:center;gap:6px;padding:8px 16px;background:var(--accent);color:#fff;border-radius:8px;font-size:12px;font-weight:600;letter-spacing:1px;cursor:pointer;transition:all 0.15s;border:none;}
  .cta-btn:hover{background:#ff3a06;transform:translateY(-1px);}
  .cta-btn .icon{font-size:16px;}

  /* Content */
  .content{padding:28px 32px;flex:1;}

  /* Hero */
  .hero{position:relative;border-radius:var(--card-r);overflow:hidden;background:var(--bg3);border:1px solid var(--border);padding:36px 40px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;}
  .hero-bg{position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,77,28,0.08) 0%,transparent 60%);pointer-events:none;}
  .hero-ring{position:absolute;right:-60px;top:50%;transform:translateY(-50%);width:260px;height:260px;border-radius:50%;border:1px solid rgba(255,77,28,0.12);pointer-events:none;}
  .hero-ring2{position:absolute;right:-100px;top:50%;transform:translateY(-50%);width:340px;height:340px;border-radius:50%;border:1px solid rgba(255,77,28,0.06);pointer-events:none;}
  .hero-tag{display:inline-flex;align-items:center;gap:6px;background:rgba(255,77,28,0.12);border:1px solid rgba(255,77,28,0.2);padding:4px 10px;border-radius:20px;margin-bottom:14px;}
  .hero-tag .dot{width:6px;height:6px;border-radius:50%;background:var(--accent);animation:pulse 2s infinite;}
  .hero-tag span{font-size:11px;font-weight:500;color:var(--accent);letter-spacing:1px;}
  .hero h1{font-family:'Barlow Condensed',sans-serif;font-weight:900;font-style:italic;font-size:48px;line-height:0.95;letter-spacing:-1px;margin-bottom:10px;}
  .hero h1 span{color:var(--accent);}
  .hero p{font-size:13px;color:var(--muted);max-width:380px;line-height:1.6;}
  .hero-stats{display:flex;gap:24px;margin-top:20px;}
  .hero-stat{display:flex;flex-direction:column;}
  .hero-stat .val{font-family:'Barlow Condensed',sans-serif;font-weight:800;font-size:28px;color:var(--text);line-height:1;}
  .hero-stat .lbl{font-size:10px;color:var(--muted);letter-spacing:2px;margin-top:3px;}
  .hero-stat-sep{width:1px;background:var(--border);}

  /* Streak ring */
  .streak-widget{position:relative;width:140px;height:140px;flex-shrink:0;}
  .streak-widget svg{position:absolute;inset:0;width:100%;height:100%;}
  .streak-inner{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;}
  .streak-inner .num{font-family:'Barlow Condensed',sans-serif;font-weight:900;font-size:36px;color:var(--accent);line-height:1;}
  .streak-inner .lbl{font-size:9px;letter-spacing:2px;color:var(--muted);}

  /* Stats row */
  .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;}
  .stat-card{background:var(--bg3);border:1px solid var(--border);border-radius:var(--card-r);padding:18px 20px;transition:all 0.2s;cursor:default;position:relative;overflow:hidden;}
  .stat-card:hover{border-color:rgba(255,77,28,0.25);transform:translateY(-2px);}
  .stat-card::before{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--accent),var(--accent2));opacity:0;transition:opacity 0.2s;}
  .stat-card:hover::before{opacity:1;}
  .stat-card .card-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;}
  .stat-card .card-icon{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;}
  .stat-card .card-icon .icon{font-size:18px;}
  .stat-card .delta{font-size:11px;font-weight:500;padding:2px 8px;border-radius:20px;}
  .delta-up{background:rgba(34,197,94,0.1);color:#4ade80;}
  .delta-down{background:rgba(239,68,68,0.1);color:#f87171;}
  .stat-card .val{font-family:'Barlow Condensed',sans-serif;font-weight:800;font-size:34px;line-height:1;margin-bottom:4px;}
  .stat-card .lbl{font-size:11px;color:var(--muted);letter-spacing:1px;}

  /* Bento grid */
  .bento{display:grid;grid-template-columns:1fr 1fr 1fr;grid-template-rows:auto;gap:12px;margin-bottom:24px;}
  .bento-card{background:var(--bg3);border:1px solid var(--border);border-radius:var(--card-r);overflow:hidden;transition:all 0.2s;}
  .bento-card:hover{border-color:rgba(255,255,255,0.12);}
  .bento-card .card-header{padding:18px 20px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
  .bento-card .card-header h3{font-size:11px;font-weight:500;letter-spacing:2px;color:var(--muted);}
  .bento-card .card-body{padding:20px;}
  .bento-span2{grid-column:span 2;}

  /* Progress bar */
  .prog-row{display:flex;align-items:center;gap:12px;margin-bottom:12px;}
  .prog-label{font-size:12px;color:var(--muted);width:100px;flex-shrink:0;}
  .prog-track{flex:1;height:5px;background:var(--bg4);border-radius:3px;overflow:hidden;}
  .prog-fill{height:100%;border-radius:3px;transition:width 1s ease;}
  .prog-val{font-size:12px;font-weight:500;width:36px;text-align:right;flex-shrink:0;}

  /* Weekly chart */
  .day-bars{display:flex;align-items:flex-end;gap:6px;height:80px;}
  .day-bar-wrap{display:flex;flex-direction:column;align-items:center;gap:6px;flex:1;}
  .day-bar{width:100%;border-radius:4px 4px 0 0;transition:all 0.3s;cursor:pointer;position:relative;}
  .day-bar.active{background:linear-gradient(180deg,var(--accent),rgba(255,77,28,0.5))!important;}
  .day-bar:hover{opacity:0.8;}
  .day-lbl{font-size:9px;color:var(--muted);letter-spacing:1px;}

  /* Exercise list */
  .exercise-item{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);}
  .exercise-item:last-child{border-bottom:none;padding-bottom:0;}
  .ex-icon{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
  .ex-icon .icon{font-size:16px;}
  .ex-info{flex:1;}
  .ex-info .name{font-size:13px;font-weight:500;margin-bottom:2px;}
  .ex-info .meta{font-size:11px;color:var(--muted);}
  .ex-sets{text-align:right;}
  .ex-sets .sets{font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:18px;color:var(--text);}
  .ex-sets .lbl{font-size:10px;color:var(--muted);}
  .ex-pill{font-size:10px;padding:2px 8px;border-radius:20px;background:rgba(255,77,28,0.1);color:var(--accent);font-weight:500;}

  /* Schedule */
  .schedule-item{display:flex;align-items:center;gap:14px;padding:10px 0;border-bottom:1px solid var(--border);}
  .schedule-item:last-child{border-bottom:none;}
  .sched-day{font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:13px;color:var(--accent);width:28px;flex-shrink:0;}
  .sched-info .name{font-size:13px;font-weight:500;}
  .sched-info .time{font-size:11px;color:var(--muted);}
  .sched-status{margin-left:auto;width:8px;height:8px;border-radius:50%;flex-shrink:0;}

  /* PRs */
  .pr-item{display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);}
  .pr-item:last-child{border-bottom:none;}
  .pr-name{font-size:12px;color:var(--muted);}
  .pr-val{font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:20px;}
  .pr-badge{font-size:10px;padding:2px 8px;border-radius:20px;background:rgba(251,191,36,0.1);color:#fbbf24;margin-left:8px;}

  /* Motivational */
  .moto-card{position:relative;border-radius:var(--card-r);overflow:hidden;background:var(--bg3);border:1px solid var(--border);padding:32px 36px;margin-bottom:0;}
  .moto-bg{position:absolute;inset:0;background:radial-gradient(ellipse at 80% 50%,rgba(255,77,28,0.06) 0%,transparent 70%);pointer-events:none;}
  .moto-quote{font-family:'Barlow Condensed',sans-serif;font-weight:900;font-style:italic;font-size:40px;line-height:1.05;letter-spacing:-1px;margin-bottom:8px;}
  .moto-author{font-size:11px;color:var(--muted);letter-spacing:2px;}
  .moto-accent{position:absolute;right:36px;top:50%;transform:translateY(-50%);font-family:'Barlow Condensed',sans-serif;font-weight:900;font-style:italic;font-size:120px;color:rgba(255,77,28,0.05);line-height:1;pointer-events:none;user-select:none;}

  /* Mobile */
  .mobile-topbar{display:none;align-items:center;justify-content:space-between;padding:14px 16px;background:var(--bg2);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:30;}
  .mobile-menu-btn{width:36px;height:36px;border-radius:8px;border:1px solid var(--border);background:var(--bg3);display:flex;align-items:center;justify-content:center;cursor:pointer;}
  .mobile-menu-btn .icon{font-size:20px;color:var(--muted);}

  @media(max-width:1100px){.stats-grid{grid-template-columns:repeat(2,1fr);} .bento{grid-template-columns:1fr 1fr;} .bento-span2{grid-column:span 2;}}
  @media(max-width:720px){
    .sidebar{display:none;}
    .mobile-topbar{display:flex;}
    .content{padding:20px 16px;}
    .stats-grid{grid-template-columns:1fr 1fr;}
    .bento{grid-template-columns:1fr;}
    .bento-span2{grid-column:span 1;}
    .hero{flex-direction:column;padding:24px;}
    .hero h1{font-size:34px;}
    .streak-widget{width:100px;height:100px;}
    .streak-inner .num{font-size:26px;}
  }

  @keyframes pulse{0%,100%{opacity:1;}50%{opacity:0.4;}}
  @keyframes fadeUp{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);}}
  .anim{animation:fadeUp 0.4s ease both;}
  .anim-1{animation-delay:0.05s;}
  .anim-2{animation-delay:0.10s;}
  .anim-3{animation-delay:0.15s;}
  .anim-4{animation-delay:0.20s;}
  .anim-5{animation-delay:0.25s;}
  .anim-6{animation-delay:0.30s;}
</style>
</head>
<body>
<div class="layout">

<!-- Sidebar -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="wordmark">THE KINETIC</div>
    <div class="sub">EDITORIAL · ELITE</div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">MENU</div>
    <a class="nav-item active" href="#">
      <span class="icon">fitness_center</span>
      <span>Dashboard</span>
    </a>
    <a class="nav-item" href="#">
      <span class="icon">exercise</span>
      <span>Workouts</span>
      <span class="badge">3</span>
    </a>
    <a class="nav-item" href="#">
      <span class="icon">monitor_weight</span>
      <span>Progress</span>
    </a>
    <a class="nav-item" href="#">
      <span class="icon">calendar_today</span>
      <span>Schedule</span>
    </a>
    <div class="nav-section-label" style="margin-top:24px;">ANALYTICS</div>
    <a class="nav-item" href="#">
      <span class="icon">bar_chart</span>
      <span>Stats</span>
    </a>
    <a class="nav-item" href="#">
      <span class="icon">emoji_events</span>
      <span>PRs</span>
    </a>
    <a class="nav-item" href="#">
      <span class="icon">history</span>
      <span>History</span>
    </a>
    <div class="nav-section-label" style="margin-top:24px;">ACCOUNT</div>
    <a class="nav-item" href="#">
      <span class="icon">person</span>
      <span>Profile</span>
    </a>
    <a class="nav-item" href="#">
      <span class="icon">settings</span>
      <span>Settings</span>
    </a>
  </nav>
  <div class="sidebar-bottom">
    <div class="user-chip">
      <div class="avatar">AK</div>
      <div>
        <div class="name">Alex Kumar</div>
        <div class="role">Elite Athlete</div>
      </div>
    </div>
  </div>
</aside>

<!-- Main -->
<div class="main">

  <!-- Mobile topbar -->
  <div class="mobile-topbar">
    <div class="wordmark" style="font-family:'Barlow Condensed',sans-serif;font-weight:900;font-style:italic;font-size:16px;color:var(--accent);letter-spacing:2px;">THE KINETIC</div>
    <div class="mobile-menu-btn"><span class="icon">menu</span></div>
  </div>

  <!-- Desktop topbar -->
  <div class="topbar" style="display:none;" id="desktopTopbar">
  </div>

  <!-- Topbar -->
  <div class="topbar">
    <div class="topbar-left">
      <div class="topbar-breadcrumb">DASHBOARD · WEEK 14</div>
      <div class="topbar-title">Good morning, Alex 🔥</div>
    </div>
    <div class="topbar-actions">
      <div class="icon-btn"><span class="icon">notifications</span></div>
      <div class="icon-btn"><span class="icon">search</span></div>
      <button class="cta-btn">
        <span class="icon">bolt</span>
        START WORKOUT
      </button>
    </div>
  </div>

  <div class="content">

    <!-- Hero -->
    <div class="hero anim">
      <div class="hero-bg"></div>
      <div class="hero-ring"></div>
      <div class="hero-ring2"></div>
      <div>
        <div class="hero-tag">
          <div class="dot"></div>
          <span>ACTIVE PROGRAM</span>
        </div>
        <h1>PUSH.<br/>PULL. <span>GROW.</span></h1>
        <p>You're on a 14-day streak. Your volume is up 18% from last month. Keep the momentum — greatness is earned, not given.</p>
        <div class="hero-stats">
          <div class="hero-stat">
            <div class="val">247</div>
            <div class="lbl">SESSIONS</div>
          </div>
          <div class="hero-stat-sep"></div>
          <div class="hero-stat">
            <div class="val">84K</div>
            <div class="lbl">TOTAL LBS</div>
          </div>
          <div class="hero-stat-sep"></div>
          <div class="hero-stat">
            <div class="val">6/8</div>
            <div class="lbl">THIS WEEK</div>
          </div>
        </div>
      </div>
      <div class="streak-widget">
        <svg viewBox="0 0 140 140" fill="none">
          <circle cx="70" cy="70" r="58" stroke="rgba(255,255,255,0.06)" stroke-width="8"/>
          <circle cx="70" cy="70" r="58" stroke="url(#sg)" stroke-width="8" stroke-linecap="round" stroke-dasharray="364.4" stroke-dashoffset="91" transform="rotate(-90 70 70)"/>
          <defs>
            <linearGradient id="sg" x1="0" y1="0" x2="1" y2="0">
              <stop offset="0%" stop-color="#FF4D1C"/>
              <stop offset="100%" stop-color="#FFB347"/>
            </linearGradient>
          </defs>
        </svg>
        <div class="streak-inner">
          <div class="num">14</div>
          <div class="lbl">DAY STREAK</div>
        </div>
      </div>
    </div>

    <!-- Stats grid -->
    <div class="stats-grid">
      <div class="stat-card anim anim-1">
        <div class="card-top">
          <div class="card-icon" style="background:rgba(255,77,28,0.1);">
            <span class="icon" style="color:var(--accent);">rocket_launch</span>
          </div>
          <span class="delta delta-up">+12%</span>
        </div>
        <div class="val" id="sv1">247</div>
        <div class="lbl">TOTAL WORKOUTS</div>
      </div>
      <div class="stat-card anim anim-2">
        <div class="card-top">
          <div class="card-icon" style="background:rgba(59,130,246,0.1);">
            <span class="icon" style="color:#60a5fa;">calendar_today</span>
          </div>
          <span class="delta delta-up">+3</span>
        </div>
        <div class="val" id="sv2">6</div>
        <div class="lbl">THIS WEEK</div>
      </div>
      <div class="stat-card anim anim-3">
        <div class="card-top">
          <div class="card-icon" style="background:rgba(168,85,247,0.1);">
            <span class="icon" style="color:#c084fc;">fitness_center</span>
          </div>
          <span class="delta delta-up">+8%</span>
        </div>
        <div class="val" id="sv3">1,842</div>
        <div class="lbl">TOTAL EXERCISES</div>
      </div>
      <div class="stat-card anim anim-4">
        <div class="card-top">
          <div class="card-icon" style="background:rgba(251,191,36,0.1);">
            <span class="icon" style="color:#fbbf24;">local_fire_department</span>
          </div>
          <span class="delta delta-up">+18%</span>
        </div>
        <div class="val">84K</div>
        <div class="lbl">TOTAL VOLUME LBS</div>
      </div>
    </div>

    <!-- Bento grid -->
    <div class="bento">

      <!-- Weekly volume chart -->
      <div class="bento-card bento-span2 anim anim-1">
        <div class="card-header">
          <h3>WEEKLY VOLUME</h3>
          <div style="display:flex;gap:8px;align-items:center;">
            <span style="font-size:11px;color:var(--muted);">Apr 2026</span>
            <div style="width:8px;height:8px;border-radius:50%;background:var(--accent);"></div>
          </div>
        </div>
        <div class="card-body">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div>
              <div style="font-family:'Barlow Condensed',sans-serif;font-weight:800;font-size:32px;line-height:1;">84,200 <span style="font-size:16px;color:var(--muted);font-weight:500;">lbs</span></div>
              <div style="font-size:11px;color:#4ade80;margin-top:2px;">↑ 18% from last week</div>
            </div>
            <div style="display:flex;gap:16px;">
              <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--muted);"><div style="width:10px;height:10px;border-radius:2px;background:var(--accent);flex-shrink:0;"></div>Volume</div>
              <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--muted);"><div style="width:10px;height:10px;border-radius:2px;background:var(--bg4);flex-shrink:0;"></div>Target</div>
            </div>
          </div>
          <div class="day-bars">
            <div class="day-bar-wrap">
              <div class="day-bar" style="height:45px;background:var(--bg4);"></div>
              <div class="day-lbl">MON</div>
            </div>
            <div class="day-bar-wrap">
              <div class="day-bar active" style="height:68px;"></div>
              <div class="day-lbl" style="color:var(--accent);">TUE</div>
            </div>
            <div class="day-bar-wrap">
              <div class="day-bar" style="height:30px;background:var(--bg4);opacity:0.5;"></div>
              <div class="day-lbl">WED</div>
            </div>
            <div class="day-bar-wrap">
              <div class="day-bar" style="height:75px;background:rgba(255,77,28,0.4);"></div>
              <div class="day-lbl">THU</div>
            </div>
            <div class="day-bar-wrap">
              <div class="day-bar" style="height:55px;background:rgba(255,77,28,0.3);"></div>
              <div class="day-lbl">FRI</div>
            </div>
            <div class="day-bar-wrap">
              <div class="day-bar" style="height:20px;background:var(--bg4);opacity:0.4;"></div>
              <div class="day-lbl">SAT</div>
            </div>
            <div class="day-bar-wrap">
              <div class="day-bar" style="height:0px;background:var(--bg4);opacity:0.2;"></div>
              <div class="day-lbl">SUN</div>
            </div>
          </div>
          <div style="height:1px;background:var(--border);margin-top:12px;"></div>
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:14px;">
            <div>
              <div style="font-size:10px;color:var(--muted);letter-spacing:1px;margin-bottom:3px;">AVG SESSION</div>
              <div style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:20px;">67 min</div>
            </div>
            <div>
              <div style="font-size:10px;color:var(--muted);letter-spacing:1px;margin-bottom:3px;">BEST DAY</div>
              <div style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:20px;">Thursday</div>
            </div>
            <div>
              <div style="font-size:10px;color:var(--muted);letter-spacing:1px;margin-bottom:3px;">COMPLETION</div>
              <div style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:20px;color:#4ade80;">75%</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Muscle groups -->
      <div class="bento-card anim anim-2">
        <div class="card-header">
          <h3>MUSCLE FOCUS</h3>
          <span class="icon" style="font-size:16px;color:var(--muted);">pie_chart</span>
        </div>
        <div class="card-body">
          <div class="prog-row">
            <div class="prog-label">Chest</div>
            <div class="prog-track"><div class="prog-fill" style="width:82%;background:var(--accent);"></div></div>
            <div class="prog-val">82%</div>
          </div>
          <div class="prog-row">
            <div class="prog-label">Back</div>
            <div class="prog-track"><div class="prog-fill" style="width:71%;background:#60a5fa;"></div></div>
            <div class="prog-val">71%</div>
          </div>
          <div class="prog-row">
            <div class="prog-label">Legs</div>
            <div class="prog-track"><div class="prog-fill" style="width:65%;background:#c084fc;"></div></div>
            <div class="prog-val">65%</div>
          </div>
          <div class="prog-row">
            <div class="prog-label">Shoulders</div>
            <div class="prog-track"><div class="prog-fill" style="width:58%;background:#fbbf24;"></div></div>
            <div class="prog-val">58%</div>
          </div>
          <div class="prog-row">
            <div class="prog-label">Arms</div>
            <div class="prog-track"><div class="prog-fill" style="width:44%;background:#4ade80;"></div></div>
            <div class="prog-val">44%</div>
          </div>
          <div class="prog-row" style="margin-bottom:0;">
            <div class="prog-label">Core</div>
            <div class="prog-track"><div class="prog-fill" style="width:37%;background:#f87171;"></div></div>
            <div class="prog-val">37%</div>
          </div>
        </div>
      </div>

      <!-- Today's workout -->
      <div class="bento-card anim anim-3">
        <div class="card-header">
          <h3>TODAY'S SESSION</h3>
          <span class="ex-pill">PUSH DAY A</span>
        </div>
        <div class="card-body" style="padding:16px 20px;">
          <div class="exercise-item">
            <div class="ex-icon" style="background:rgba(255,77,28,0.1);"><span class="icon" style="color:var(--accent);">fitness_center</span></div>
            <div class="ex-info">
              <div class="name">Bench Press</div>
              <div class="meta">185 lbs · 3×8</div>
            </div>
            <div class="ex-sets"><div class="sets">✓</div><div class="lbl">DONE</div></div>
          </div>
          <div class="exercise-item">
            <div class="ex-icon" style="background:rgba(96,165,250,0.1);"><span class="icon" style="color:#60a5fa;">sports_gymnastics</span></div>
            <div class="ex-info">
              <div class="name">Overhead Press</div>
              <div class="meta">115 lbs · 4×6</div>
            </div>
            <div class="ex-sets"><div class="sets" style="color:var(--accent);">3/4</div><div class="lbl">SETS</div></div>
          </div>
          <div class="exercise-item">
            <div class="ex-icon" style="background:rgba(168,85,247,0.1);"><span class="icon" style="color:#c084fc;">self_improvement</span></div>
            <div class="ex-info">
              <div class="name">Incline DB Fly</div>
              <div class="meta">40 lbs · 3×12</div>
            </div>
            <div class="ex-sets"><div class="sets" style="color:var(--muted);">–</div><div class="lbl">NEXT</div></div>
          </div>
          <div class="exercise-item" style="border:none;padding-bottom:0;">
            <div class="ex-icon" style="background:rgba(251,191,36,0.1);"><span class="icon" style="color:#fbbf24;">sports_martial_arts</span></div>
            <div class="ex-info">
              <div class="name">Tricep Pushdown</div>
              <div class="meta">70 lbs · 3×15</div>
            </div>
            <div class="ex-sets"><div class="sets" style="color:var(--muted);">–</div><div class="lbl">QUEUED</div></div>
          </div>
        </div>
      </div>

      <!-- Schedule -->
      <div class="bento-card anim anim-4">
        <div class="card-header">
          <h3>THIS WEEK</h3>
          <span class="icon" style="font-size:16px;color:var(--muted);">event</span>
        </div>
        <div class="card-body" style="padding:16px 20px;">
          <div class="schedule-item">
            <div class="sched-day">MON</div>
            <div class="sched-info"><div class="name">Push Day A</div><div class="time">7:00 AM · 65 min</div></div>
            <div class="sched-status" style="background:#4ade80;"></div>
          </div>
          <div class="schedule-item">
            <div class="sched-day">TUE</div>
            <div class="sched-info"><div class="name">Pull Day</div><div class="time">7:00 AM · 72 min</div></div>
            <div class="sched-status" style="background:#4ade80;"></div>
          </div>
          <div class="schedule-item">
            <div class="sched-day">WED</div>
            <div class="sched-info"><div class="name" style="color:var(--muted);">Rest Day</div><div class="time">Active Recovery</div></div>
            <div class="sched-status" style="background:var(--muted);opacity:0.4;"></div>
          </div>
          <div class="schedule-item">
            <div class="sched-day">THU</div>
            <div class="sched-info"><div class="name">Leg Day</div><div class="time">6:30 AM · 80 min</div></div>
            <div class="sched-status" style="background:#4ade80;"></div>
          </div>
          <div class="schedule-item">
            <div class="sched-day" style="color:var(--accent);">FRI</div>
            <div class="sched-info"><div class="name" style="color:var(--accent);">Push Day B</div><div class="time" style="color:var(--accent);opacity:0.7;">NOW · In progress</div></div>
            <div class="sched-status" style="background:var(--accent);animation:pulse 1.5s infinite;"></div>
          </div>
          <div class="schedule-item">
            <div class="sched-day" style="color:var(--muted);">SAT</div>
            <div class="sched-info"><div class="name" style="color:var(--muted);">Cardio</div><div class="time">TBD</div></div>
            <div class="sched-status" style="background:var(--border);border:1px solid rgba(255,255,255,0.1);"></div>
          </div>
        </div>
      </div>

      <!-- PRs -->
      <div class="bento-card anim anim-5">
        <div class="card-header">
          <h3>PERSONAL RECORDS</h3>
          <span class="icon" style="font-size:16px;color:#fbbf24;">emoji_events</span>
        </div>
        <div class="card-body" style="padding:16px 20px;">
          <div class="pr-item">
            <div class="pr-name">Bench Press</div>
            <div style="display:flex;align-items:center;">
              <div class="pr-val">225</div>
              <span style="font-size:12px;color:var(--muted);margin-left:4px;">lbs</span>
              <span class="pr-badge">NEW</span>
            </div>
          </div>
          <div class="pr-item">
            <div class="pr-name">Squat</div>
            <div style="display:flex;align-items:center;">
              <div class="pr-val">315</div>
              <span style="font-size:12px;color:var(--muted);margin-left:4px;">lbs</span>
            </div>
          </div>
          <div class="pr-item">
            <div class="pr-name">Deadlift</div>
            <div style="display:flex;align-items:center;">
              <div class="pr-val">405</div>
              <span style="font-size:12px;color:var(--muted);margin-left:4px;">lbs</span>
            </div>
          </div>
          <div class="pr-item">
            <div class="pr-name">OHP</div>
            <div style="display:flex;align-items:center;">
              <div class="pr-val">145</div>
              <span style="font-size:12px;color:var(--muted);margin-left:4px;">lbs</span>
            </div>
          </div>
          <div class="pr-item">
            <div class="pr-name">Pull-ups</div>
            <div style="display:flex;align-items:center;">
              <div class="pr-val">22</div>
              <span style="font-size:12px;color:var(--muted);margin-left:4px;">reps</span>
              <span class="pr-badge">NEW</span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Motivational -->
    <div class="moto-card anim anim-6">
      <div class="moto-bg"></div>
      <div class="moto-accent">"</div>
      <div style="position:relative;z-index:1;">
        <div style="width:32px;height:3px;background:var(--accent);margin-bottom:20px;border-radius:2px;"></div>
        <div class="moto-quote">"The iron never lies to you.<br/>You can walk outside and <span style="color:var(--accent);">get disapproved of.</span>"</div>
        <div class="moto-author">— HENRY ROLLINS · IRON AND THE SOUL</div>
      </div>
    </div>

  </div>
</div>
</div>
</body>
</html>
