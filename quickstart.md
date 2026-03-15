# 🚀 QUICK START GUIDE - Gym Workout Planner

## ⚡ 5-Minute Setup

### Step 1: Install XAMPP
1. Download XAMPP from https://www.apachefriends.org
2. Install it
3. Open XAMPP Control Panel
4. Click **Start** on **Apache** and **MySQL** (both should turn green)

### Step 2: Create Database
1. Open browser → http://localhost/phpmyadmin
2. Click **"New"** (left sidebar)
3. Database name: `gym_planner`
4. Click **"Create"**

### Step 3: Import Database
1. Click on `gym_planner` database (left sidebar)
2. Click **"SQL"** tab
3. Open `gym_planner.sql` file in notepad
4. Copy ALL the SQL code
5. Paste into the SQL box
6. Click **"Go"** button
7. You should see: "8 tables created"

### Step 4: Copy Project Files
1. Create folder structure in `C:\xampp\htdocs\`:
```
C:\xampp\htdocs\gym-planner\
├── index.html
├── dashboard.html
├── workouts.html
├── progress.html
├── css\
│   ├── style.css
│   ├── auth.css
│   └── dashboard.css
├── js\
│   ├── config.js
│   ├── api.js
│   ├── auth.js
│   ├── workouts.js
│   └── progress.js
└── php\
    ├── config.php
    ├── functions.php
    ├── auth.php
    ├── workouts.php
    └── progress.php
```

2. Copy all downloaded files to their respective folders

### Step 5: Test It!
1. Open browser
2. Go to: **http://localhost/gym-planner/**
3. Create an account
4. Start using! 🎉

---

## 📂 File Checklist

Make sure you have ALL these files:

**HTML Files (4):**
- ✅ index.html
- ✅ dashboard.html
- ✅ workouts.html
- ✅ progress.html

**CSS Files (3 in css/ folder):**
- ✅ style.css
- ✅ auth.css
- ✅ dashboard.css

**JavaScript Files (5 in js/ folder):**
- ✅ config.js
- ✅ api.js
- ✅ auth.js
- ✅ workouts.js
- ✅ progress.js

**PHP Files (5 in php/ folder):**
- ✅ config.php
- ✅ functions.php
- ✅ auth.php
- ✅ workouts.php
- ✅ progress.php

**Database:**
- ✅ gym_planner.sql

---

## 🐛 Common Issues

### "Can't connect to database"
→ Make sure MySQL is running (green in XAMPP)

### "404 Not Found"
→ Check files are in `C:\xampp\htdocs\gym-planner\`

### "Blank page"
→ Press F12, check Console tab for errors

### Charts not showing
→ Need internet connection for Chart.js library

---

## ✅ Success Checklist

After setup, you should be able to:
- [ ] Open http://localhost/gym-planner/
- [ ] See login page
- [ ] Create an account
- [ ] Login successfully
- [ ] See dashboard with your name
- [ ] Create a workout
- [ ] Add progress entry
- [ ] See charts

---

## 📞 Need Help?

1. Check README.md for detailed guide
2. Make sure ALL files are copied correctly
3. Verify database has 8 tables
4. Check Apache and MySQL are running

---

**That's it! You're ready to track your gains! 💪**