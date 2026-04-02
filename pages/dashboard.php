<!DOCTYPE html>

<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<link href="https://fonts.googleapis.com/css2?family=Epilogue:ital,wght@0,400;0,700;0,800;0,900;1,900&amp;family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "outline-variant": "#484847",
              "inverse-on-surface": "#565555",
              "tertiary-container": "#ffd709",
              "tertiary-dim": "#efc900",
              "on-secondary-fixed": "#532a00",
              "error-container": "#9f0519",
              "surface-container-highest": "#262626",
              "surface-variant": "#262626",
              "secondary-container": "#904d00",
              "on-secondary-fixed-variant": "#7c4100",
              "primary-fixed": "#ff7852",
              "primary-fixed-dim": "#ff5d2e",
              "surface": "#0e0e0e",
              "inverse-primary": "#b22e00",
              "on-error": "#490006",
              "on-secondary": "#442100",
              "secondary-fixed": "#ffc69a",
              "primary-container": "#ff7852",
              "secondary-fixed-dim": "#ffb375",
              "surface-tint": "#ff8f70",
              "on-tertiary-fixed-variant": "#665500",
              "primary-dim": "#ff734c",
              "surface-container-lowest": "#000000",
              "on-tertiary": "#655400",
              "on-primary-container": "#480d00",
              "error": "#ff716c",
              "on-primary-fixed": "#000000",
              "on-primary": "#5c1300",
              "secondary-dim": "#ed8200",
              "surface-container-low": "#131313",
              "on-secondary-container": "#fff6f1",
              "surface-container-high": "#20201f",
              "inverse-surface": "#fcf9f8",
              "tertiary-fixed": "#ffd709",
              "secondary": "#fd8b00",
              "surface-bright": "#2c2c2c",
              "on-tertiary-fixed": "#453900",
              "tertiary": "#ffe792",
              "on-tertiary-container": "#5b4b00",
              "on-background": "#ffffff",
              "background": "#0e0e0e",
              "on-primary-fixed-variant": "#581200",
              "on-surface-variant": "#adaaaa",
              "tertiary-fixed-dim": "#efc900",
              "surface-dim": "#0e0e0e",
              "on-error-container": "#ffa8a3",
              "primary": "#ff8f70",
              "outline": "#767575",
              "surface-container": "#1a1a1a",
              "on-surface": "#ffffff",
              "error-dim": "#d7383b"
            },
            fontFamily: {
              "headline": ["Epilogue"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
            borderRadius: {"DEFAULT": "1rem", "lg": "2rem", "xl": "3rem", "full": "9999px"},
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .neon-glow-orange {
            box-shadow: 0 0 15px rgba(255, 115, 76, 0.3);
        }
        .kinetic-gradient {
            background: linear-gradient(135deg, #ff8f70 0%, #ff5d2e 100%);
        }
    </style>
</head>
<body class="bg-surface text-on-surface font-body selection:bg-primary selection:text-on-primary-fixed">
<!-- Top Navigation Shell -->
<header class="fixed top-0 w-full z-50 flex justify-between items-center px-8 py-6 w-full bg-transparent">
<div class="text-2xl font-black italic text-orange-600 dark:text-orange-500 tracking-widest font-headline">THE KINETIC EDITORIAL</div>
<div class="flex items-center gap-6">
<span class="material-symbols-outlined text-orange-500 text-2xl cursor-pointer hover:text-orange-400 transition-colors scale-95 active:scale-90" data-icon="fitness_center">fitness_center</span>
</div>
</header>
<!-- Side Navigation Shell -->
<aside class="fixed left-0 top-0 h-full w-64 z-40 flex flex-col gap-8 p-8 bg-neutral-900/60 backdrop-blur-xl shadow-[20px_0_40px_rgba(0,0,0,0.4)] hidden md:flex">
<div class="mt-20">
<div class="flex flex-col gap-1 mb-12">
<span class="font-headline uppercase tracking-widest text-xs font-bold text-orange-500">KINETIC ATHLETE</span>
<span class="font-body text-[10px] text-neutral-500 tracking-[0.2em]">ELITE LEVEL</span>
</div>
<nav class="flex flex-col gap-4">
<a class="flex items-center gap-3 font-headline uppercase tracking-widest text-xs font-bold text-orange-500 bg-orange-500/10 rounded-full px-4 py-2 shadow-[0_0_15px_rgba(255,69,0,0.3)] transition-transform active:scale-95" href="#">
<span class="material-symbols-outlined" data-icon="exercise">exercise</span>
<span>Training</span>
</a>
<a class="flex items-center gap-3 font-headline uppercase tracking-widest text-xs font-bold text-neutral-500 hover:text-neutral-200 px-4 py-2 transition-all hover:bg-neutral-800/50" href="#">
<span class="material-symbols-outlined" data-icon="history">history</span>
<span>History</span>
</a>
<a class="flex items-center gap-3 font-headline uppercase tracking-widest text-xs font-bold text-neutral-500 hover:text-neutral-200 px-4 py-2 transition-all hover:bg-neutral-800/50" href="#">
<span class="material-symbols-outlined" data-icon="person">person</span>
<span>Profile</span>
</a>
</nav>
</div>
</aside>
<!-- Main Content Canvas -->
<main class="page active pt-32 pb-32 md:pl-72 px-6 md:pr-12 min-h-screen" id="dashboardPage">
<!-- Welcome Section -->
<section class="mb-12">
<h1 class="font-headline font-black text-5xl md:text-7xl tracking-tighter mb-4 text-on-surface">
                Welcome Back, <span class="text-transparent bg-clip-text kinetic-gradient">Athlete 💪</span>
</h1>
<p class="font-body text-on-surface-variant text-lg max-w-xl">
                Track your progress and stay consistent. Your velocity defines your evolution.
            </p>
</section>
<!-- Stat Grid (Bento Style) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
<div class="stat-card glass-card rounded-xl p-8 flex flex-col justify-between aspect-square md:aspect-auto md:h-48 neon-glow-orange group" id="stat-card-1">
<div class="flex justify-between items-start">
<span class="material-symbols-outlined text-primary text-4xl" data-icon="rocket_launch">rocket_launch</span>
<span class="text-xs font-headline font-bold text-primary tracking-widest uppercase">Velocity</span>
</div>
<div>
<div class="text-5xl font-headline font-black mb-1" id="stat-value-total">0</div>
<div class="text-xs font-label uppercase tracking-widest text-on-surface-variant">Total Workouts</div>
</div>
</div>
<div class="stat-card glass-card rounded-xl p-8 flex flex-col justify-between aspect-square md:aspect-auto md:h-48 group" id="stat-card-2">
<div class="flex justify-between items-start">
<span class="material-symbols-outlined text-secondary text-4xl" data-icon="calendar_today">calendar_today</span>
<span class="text-xs font-headline font-bold text-secondary tracking-widest uppercase">Consistency</span>
</div>
<div>
<div class="text-5xl font-headline font-black mb-1" id="stat-value-week">0</div>
<div class="text-xs font-label uppercase tracking-widest text-on-surface-variant">This Week</div>
</div>
</div>
<div class="stat-card glass-card rounded-xl p-8 flex flex-col justify-between aspect-square md:aspect-auto md:h-48 group" id="stat-card-3">
<div class="flex justify-between items-start">
<span class="material-symbols-outlined text-white text-4xl" data-icon="fitness_center">fitness_center</span>
<span class="text-xs font-headline font-bold text-white tracking-widest uppercase">Volume</span>
</div>
<div>
<div class="text-5xl font-headline font-black mb-1" id="stat-value-exercises">0</div>
<div class="text-xs font-label uppercase tracking-widest text-on-surface-variant">Total Exercises</div>
</div>
</div>
</div>
<!-- Weekly Progress -->
<section class="mb-12">
<div class="glass-card rounded-xl p-8 md:p-12 relative overflow-hidden">
<div class="flex flex-col md:flex-row justify-between items-end gap-6 relative z-10">
<div class="w-full">
<div class="flex justify-between items-center mb-6">
<h2 class="font-headline font-bold text-2xl uppercase tracking-tighter">Weekly Progress</h2>
<span class="font-headline font-black text-4xl text-primary">75%</span>
</div>
<div class="h-4 w-full bg-surface-container-highest rounded-full overflow-hidden">
<div class="h-full kinetic-gradient rounded-full neon-glow-orange" style="width: 75%"></div>
</div>
<div class="mt-6 flex gap-4">
<div class="px-4 py-1 rounded-full bg-surface-container-low text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">6/8 Sessions Complete</div>
<div class="px-4 py-1 rounded-full bg-primary/10 text-[10px] font-bold uppercase tracking-widest text-primary">+12% vs last week</div>
</div>
</div>
</div>
<!-- Background decoration -->
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 blur-[100px] rounded-full -translate-y-1/2 translate-x-1/2"></div>
</div>
</section>
<!-- Quick Actions (Asymmetric Grid) -->
<section class="mb-12">
<h3 class="font-headline font-bold text-xs uppercase tracking-[0.3em] text-neutral-500 mb-6">Immediate Actions</h3>
<div class="grid grid-cols-1 md:grid-cols-12 gap-6">
<button class="md:col-span-6 kinetic-gradient rounded-xl p-10 flex flex-col justify-end items-start text-left group transition-all hover:scale-[1.02] active:scale-95 min-h-[280px] relative overflow-hidden">
<span class="material-symbols-outlined text-6xl text-black/20 absolute top-8 right-8 group-hover:scale-110 transition-transform" data-icon="bolt">bolt</span>
<span class="text-on-primary-fixed font-headline font-black text-4xl mb-2">Start Workout</span>
<span class="text-on-primary-fixed/70 font-body text-sm">Jump back into your active split immediately.</span>
</button>
<button class="md:col-span-3 glass-card rounded-xl p-8 flex flex-col justify-between items-start text-left transition-all hover:bg-white/10 active:scale-95 group">
<span class="material-symbols-outlined text-primary text-4xl" data-icon="add_circle">add_circle</span>
<div>
<div class="font-headline font-bold text-xl mb-1">Build Workout</div>
<p class="text-on-surface-variant text-xs font-body">Craft a bespoke session</p>
</div>
</button>
<button class="md:col-span-3 glass-card rounded-xl p-8 flex flex-col justify-between items-start text-left transition-all hover:bg-white/10 active:scale-95 group">
<span class="material-symbols-outlined text-secondary text-4xl" data-icon="history">history</span>
<div>
<div class="font-headline font-bold text-xl mb-1">View History</div>
<p class="text-on-surface-variant text-xs font-body">Review past performance</p>
</div>
</button>
</div>
</section>
<!-- Motivational Banner -->
<section class="relative w-full rounded-xl overflow-hidden min-h-[400px] flex items-center justify-center mb-12">
<div class="absolute inset-0 bg-cover bg-center" data-alt="dramatic wide angle shot of a gritty industrial gym with heavy iron weights and atmospheric overhead lighting" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBaEJ9G00EXMpC0Hfmyft1aA4bs_9jPkbJNbDw5R-7FhZbn_mzBt0vNe3dS-P-ma12VOlhULmapIq1kO8nU-kcv7aJKgj6rGzSe87LkHKSsEsArs6FG49dK2j9VFepRxisHbLZA4pMqr8uA6UTIFBqD8pBDSA9C6gWeONKYxB3F_rhgXs4JkQ6tIcayM2Lyao2itp_lFbRiEf078rtK9_OSU_DoT7Pa_iOUS7jm_J10y79DakYF48Pxt6WYAFyYMUNHEr89FFvT_BR7')"></div>
<div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
<div class="relative z-10 text-center px-6">
<h2 class="font-headline font-black italic text-6xl md:text-[10rem] leading-none tracking-tighter text-white/90 uppercase mix-blend-overlay">
                    NO PAIN.<br/>NO GAIN.
                </h2>
<div class="mt-8 flex justify-center">
<div class="h-1 w-24 kinetic-gradient"></div>
</div>
</div>
</section>
</main>
<!-- Footer Shell -->
<footer class="md:pl-72 fixed bottom-0 w-full bg-transparent">
<div class="flex flex-col md:flex-row justify-between items-center px-12 py-8 w-full">
<span class="font-body text-[10px] uppercase tracking-[0.2em] font-semibold text-neutral-500">© 2024 THE KINETIC EDITORIAL. NO LIMITS.</span>
<div class="flex gap-8 mt-4 md:mt-0">
<a class="font-body text-[10px] uppercase tracking-[0.2em] font-semibold text-neutral-500 hover:text-white transition-colors" href="#">Privacy</a>
<a class="font-body text-[10px] uppercase tracking-[0.2em] font-semibold text-neutral-500 hover:text-white transition-colors" href="#">Terms</a>
<a class="font-body text-[10px] uppercase tracking-[0.2em] font-semibold text-neutral-500 hover:text-white transition-colors" href="#">Support</a>
</div>
</div>
</footer>
</body></html>